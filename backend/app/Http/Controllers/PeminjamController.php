<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    /**
     * 1. Melihat daftar/katalog alat yang tersedia
     */
    public function katalogAlat(Request $request)
    {
        $query = Alat::with('kategori')->where('stok', '>', 0);

        // Fitur pencarian alat jika ada input search
        if ($request->has('search')) {
            $query->where('nama_alat', 'like', '%' . $request->search . '%');
        }

        $alats = $query->latest()->get();
        return view('peminjam.katalog', compact('alats'));
    }

    /**
     * 2. Mengajukan peminjaman alat
     */
    public function ajukanPeminjaman(Request $request)
    {
        $request->validate([
            'tgl_kembali_plan' => 'required|date|after:today',
            'alat_id'          => 'required|array',
            'jumlah'           => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            // Buat header peminjaman
            $peminjaman = Peminjaman::create([
                'user_id'          => auth()->id(),
                'tgl_pinjam'       => now(),
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status'           => 'diajukan',
            ]);

            // Masukkan daftar alat yang dipinjam ke tabel detail_pinjam
            foreach ($request->alat_id as $index => $alatId) {
                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id'       => $alatId,
                    'jumlah'        => $request->jumlah[$index],
                ]);
            }

            DB::commit();
            return redirect()->route('peminjam.riwayat')->with('success', 'Pengajuan peminjaman berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }

    /**
     * Melihat riwayat peminjaman user yang sedang login
     */
    public function riwayatPeminjaman()
    {
        $peminjamans = Peminjaman::with('detailPinjams.alat')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('peminjam.riwayat', compact('peminjamans'));
    }

    /**
     * 3. Mengembalikan alat
     */
    public function kembalikanAlat($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            // Pastikan status peminjaman saat ini 'dipinjam'
            if ($peminjaman->status !== 'dipinjam') {
                return redirect()->back()->with('error', 'Status peminjaman tidak valid untuk dikembalikan.');
            }

            // Ubah status menjadi proses pengembalian (menunggu konfirmasi petugas)
            $peminjaman->update([
                'status' => 'proses_pengembalian'
            ]);

            DB::commit();
            return redirect()->route('peminjam.riwayat')->with('success', 'Permintaan pengembalian alat berhasil dikirim ke petugas.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengembalikan alat: ' . $e->getMessage());
        }
    }
}