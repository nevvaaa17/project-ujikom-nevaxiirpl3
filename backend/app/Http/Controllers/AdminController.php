<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    // Menampilkan Dashboard Admin & Log Aktivitas
    public function index()
    {
        $logs = LogAktivitas::with('user')->latest()->take(10)->get();
        return view('admin.dashboard', compact('logs'));
    }

    // ==========================================
    // CRUD Alat
    // ==========================================

    // 1. CRUD Alat: Menampilkan daftar alat
    public function indexAlat(Request $request)
    {
        $search = $request->input('search');

        $alats = Alat::with('kategori')
            ->when($search, function ($query, $search) {
                return $query->where('nama_alat', 'like', "%{$search}%")
                    ->orWhere('status_kondisi', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('nama_kategori', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.alat.index', compact('alats', 'search'));
    }

    // 2. Menampilkan form tambah alat
    public function createAlat()
    {
        $kategoris = Kategori::all();
        return view('admin.alat.create', compact('kategoris'));
    }

    // 3. Menyimpan alat baru
    public function storeAlat(Request $request)
    {
        $request->validate([
            'nama_alat'      => 'required|string|max:255',
            'kategori_id'    => 'required|exists:kategori,id',
            'stok'           => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:255',
            'deskripsi'      => 'nullable|string',
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        Alat::create($data);

        LogAktivitas::create([
            'user_id'   => auth()->id(),
            'aktivitas' => 'Menambahkan alat baru: ' . $request->nama_alat
        ]);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil ditambahkan.');
    }

    // 4. Menampilkan form edit alat
    public function editAlat($id)
    {
        $alat = Alat::findOrFail($id);
        $kategoris = Kategori::all();
        return view('admin.alat.edit', compact('alat', 'kategoris'));
    }

    // 5. Memperbarui data alat
    public function updateAlat(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'nama_alat'      => 'required|string|max:255',
            'kategori_id'    => 'required|exists:kategori,id',
            'stok'           => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:255',
            'deskripsi'      => 'nullable|string',
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
            if ($alat->gambar && file_exists(public_path($alat->gambar))) {
                unlink(public_path($alat->gambar));
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        $alat->update($data);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil diperbarui.');
    }

    // 6. Menghapus data alat
    public function destroyAlat($id)
    {
        $alat = Alat::findOrFail($id);

        if ($alat->gambar && file_exists(public_path($alat->gambar))) {
            unlink(public_path($alat->gambar));
        }

        $alat->delete();

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil dihapus.');
    }

    // ==========================================
    // CRUD User
    // ==========================================

    public function indexUser(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('admin.user.index', compact('users', 'search'));
    }

    public function createUser()
    {
        return view('admin.user.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,petugas,peminjam',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'no_hp'    => $request->no_hp,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role'  => 'required|in:admin,petugas,peminjam',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
            'no_hp' => $request->no_hp,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus.');
    }

    // ==========================================
    // CRUD Kategori
    // ==========================================

    public function indexKategori(Request $request)
    {
        $search = $request->input('search');

        $kategoris = Kategori::when($search, function ($query, $search) {
            return $query->where('nama_kategori', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(5)
        ->withQueryString();

        return view('admin.kategori.index', compact('kategoris', 'search'));
    }

    public function createKategori()
    {
        return view('admin.kategori.create');
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function editKategori($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function updateKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,' . $id,
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->alats()->count() > 0) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    // ==========================================
    // MANAJEMEN PEMINJAMAN (Sesuai Modul)
    // ==========================================

    // 1. Menampilkan daftar peminjaman
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPeminjaman.alat'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhere('status', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.peminjaman.index', compact('peminjamans', 'search'));
    }

    // 2. Menampilkan form tambah peminjaman
    public function createPeminjaman()
    {
        $users = User::where('role', 'peminjam')->get();
        $alats = Alat::where('stok', '>', 0)->get();

        return view('admin.peminjaman.create', compact('users', 'alats'));
    }

    // 3. Menyimpan data peminjaman baru
    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id'             => 'required|exists:users,id',
            'tgl_pinjam'          => 'required|date',
            'tgl_kembali_rencana' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id'             => 'required|array',
            'jumlah'              => 'required|array',
            'jumlah.*'            => 'required|integer|min:1',
        ]);

        $peminjaman = Peminjaman::create([
            'user_id'          => $request->user_id,
            'tgl_pinjam'       => $request->tgl_pinjam,
            'tgl_kembali_plan' => $request->tgl_kembali_rencana,
            'status'           => 'dipinjam',
        ]);

        foreach ($request->alat_id as $key => $alat_id) {
            $jumlahPinjam = $request->jumlah[$key];
            $alat = Alat::findOrFail($alat_id);

            // Simpan detail peminjaman
            $peminjaman->detailPeminjaman()->create([
                'alat_id' => $alat_id,
                'jumlah'  => $jumlahPinjam,
            ]);

            // Kurangi stok alat
            $alat->decrement('stok', $jumlahPinjam);
        }

        LogAktivitas::create([
            'user_id'   => auth()->id(),
            'aktivitas' => 'Membuat transaksi peminjaman baru untuk User ID: ' . $request->user_id
        ]);

        return redirect()->route('admin.peminjaman.index')->with('success', 'Peminjaman berhasil ditambahkan.');
    }

    // 4. Memperbarui status peminjaman (Persetujuan / Pengembalian)
    public function updateStatusPeminjaman(Request $request, $id)
    {
        $peminjaman = Peminjaman::with('detailPeminjaman.alat')->findOrFail($id);
        $statusBaru = $request->status;

        if ($statusBaru == 'disetujui' && $peminjaman->status == 'menunggu') {
            foreach ($peminjaman->detailPeminjaman as $detail) {
                if ($detail->alat->stok < $detail->jumlah) {
                    return redirect()->back()->with('error', 'Stok alat ' . $detail->alat->nama_alat . ' tidak mencukupi!');
                }
                $detail->alat->decrement('stok', $detail->jumlah);
            }
        } elseif ($statusBaru == 'dikembalikan' && $peminjaman->status == 'disetujui') {
            foreach ($peminjaman->detailPeminjaman as $detail) {
                $detail->alat->increment('stok', $detail->jumlah);
            }
            $peminjaman->tgl_kembali_realisasi = now();
        }

        $peminjaman->status = $statusBaru;
        $peminjaman->save();

        LogAktivitas::create([
            'user_id'   => auth()->id(),
            'aktivitas' => 'Mengubah status peminjaman ID ' . $id . ' menjadi ' . $statusBaru
        ]);

        return redirect()->back()->with('success', 'Status peminjaman berhasil diperbarui.');
    }

    // 5. Menghapus data peminjaman
    public function destroyPeminjaman($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status == 'disetujui') {
            foreach ($peminjaman->detailPeminjaman as $detail) {
                $detail->alat->increment('stok', $detail->jumlah);
            }
        }

        $peminjaman->delete();

        return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }

    // ==========================================
    // MANAJEMEN PENGEMBALIAN
    // ==========================================

    // 1. Menampilkan daftar pengembalian
    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $pengembalians = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPeminjaman.alat', 'petugas'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('peminjaman.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengembalian.index', compact('pengembalians', 'search'));
    }

    // 2. Menampilkan form pengembalian berdasarkan ID Peminjaman
    public function createPengembalian($peminjaman_id)
    {
        $peminjaman = Peminjaman::with(['user', 'detailPeminjaman.alat'])->findOrFail($peminjaman_id);

        // Perhitungan Estimasi Denda (Contoh: Rp 10.000 per hari Keterlambatan)
        $tglRencana = Carbon::parse($peminjaman->tgl_kembali_plan);
        $tglSekarang = Carbon::now();
        $denda = 0;

        if ($tglSekarang->greaterThan($tglRencana)) {
            $hariTerlambat = $tglSekarang->diffInDays($tglRencana);
            $denda = $hariTerlambat * 10000;
        }

        return view('admin.pengembalian.create', compact('peminjaman', 'denda'));
    }

    // 3. Menyimpan data pengembalian & memproses stok
    public function storePengembalian(Request $request, $peminjaman_id)
    {
        $request->validate([
            'tgl_kembali'     => 'required|date',
            'kondisi_kembali' => 'required|string',
            'denda'           => 'nullable|numeric|min:0',
        ]);

        $peminjaman = Peminjaman::with('detailPeminjaman.alat')->findOrFail($peminjaman_id);

        // A. Buat Record Pengembalian Baru
        $pengembalian = Pengembalian::create([
            'peminjaman_id'   => $peminjaman->id,
            'tgl_kembali'     => $request->tgl_kembali,
            'kondisi_kembali' => $request->kondisi_kembali,
            'denda'           => $request->denda ?? 0,
            'petugas_id'      => auth()->id(),
        ]);

        // B. Kembalikan Stok Alat yang Dipinjam
        foreach ($peminjaman->detailPeminjaman as $detail) {
            if ($detail->alat) {
                $detail->alat->increment('stok', $detail->jumlah);
            }
        }

        // C. Update Status Peminjaman
        $peminjaman->update([
            'status' => 'dikembalikan',
            'tgl_kembali_realisasi' => $request->tgl_kembali,
        ]);

        // D. Catat ke Log Aktivitas
        $pesanLog = "Memproses pengembalian alat untuk peminjaman ID #{$peminjaman->id}";
        if ($request->denda > 0) {
            $pesanLog .= " dan mengenakan denda sebesar Rp " . number_format($request->denda, 0, ',', '.');
        }

        LogAktivitas::create([
            'user_id'   => auth()->id(),
            'aktivitas' => $pesanLog,
        ]);

        return redirect()->route('admin.pengembalian.index')->with('success', 'Pengembalian alat berhasil diproses.');
    }

    // 4. Menghapus data pengembalian
    public function destroyPengembalian($id)
    {
        $pengembalian = Pengembalian::findOrFail($id);
        $pengembalian->delete();

        return redirect()->route('admin.pengembalian.index')->with('success', 'Data pengembalian berhasil dihapus.');
    }
}