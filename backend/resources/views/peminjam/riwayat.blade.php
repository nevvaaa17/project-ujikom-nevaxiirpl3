<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pinjam - Panel Peminjam</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 font-sans flex h-screen overflow-hidden">

    <!-- SIDEBAR KIRI -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between hidden md:flex shadow-lg">
        <div>
            <div class="p-5 text-white font-bold text-lg tracking-wide border-b border-slate-800">
                PANEL PEMINJAM
            </div>
            <nav class="p-4 space-y-2 text-sm font-medium">
                <a href="{{ route('peminjam.katalog') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition">
                    <i class="fa-solid fa-boxes-stacked"></i> Katalog & Pinjam Alat
                </a>
                <a href="{{ route('peminjam.riwayat') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg bg-blue-600 text-white shadow-sm">
                    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Pinjam
                </a>
            </nav>
        </div>
        <div class="p-4 border-t border-slate-800 text-xs text-slate-400">
            Logged in as: <span class="font-bold text-white">{{ Auth::user()->name ?? 'Peminjam' }}</span>
        </div>
    </aside>

    <!-- AREA KANAN (CONTENT) -->
    <div class="flex-1 flex flex-col overflow-hidden">
        
        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shadow-sm">
            <h1 class="text-lg font-bold text-gray-800">Riwayat Peminjaman Alat</h1>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-1.5 rounded-lg text-sm font-semibold shadow-sm transition">
                    Logout
                </button>
            </form>
        </header>

        <!-- MAIN SCROLLABLE CONTENT -->
        <main class="flex-1 overflow-y-auto p-6 space-y-6">

            <!-- NOTIFIKASI FLASH MESSAGE -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl text-sm shadow-sm flex justify-between items-center">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="font-bold">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-sm shadow-sm flex justify-between items-center">
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="font-bold">&times;</button>
                </div>
            @endif

            <!-- TABEL RIWAYAT -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h3 class="font-bold text-md text-gray-800 border-b pb-3">Daftar Riwayat & Pengembalian Alat</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 border-collapse">
                        <thead class="bg-gray-50 text-gray-700 uppercase text-xs border-b">
                            <tr>
                                <th class="p-3">No</th>
                                <th class="p-3">Tanggal Pinjam</th>
                                <th class="p-3">Rencana Kembali</th>
                                <th class="p-3">Daftar Alat & Jumlah</th>
                                <th class="p-3">Status</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($peminjamans as $index => $p)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-3">{{ $index + 1 }}</td>
                                    <td class="p-3">{{ $p->tgl_pinjam }}</td>
                                    <td class="p-3">{{ $p->tgl_kembali_plan }}</td>
                                    <td class="p-3">
                                        <ul class="list-disc list-inside text-xs space-y-1">
                                            @foreach($p->detailPinjams as $detail)
                                                <li>
                                                    <span class="font-semibold text-gray-800">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span> 
                                                    ({{ $detail->jumlah }} unit)
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold 
                                            @if($p->status == 'diajukan') bg-amber-100 text-amber-800 
                                            @elseif($p->status == 'dipinjam') bg-blue-100 text-blue-800 
                                            @elseif($p->status == 'proses_pengembalian') bg-purple-100 text-purple-800 
                                            @elseif($p->status == 'dikembalikan') bg-green-100 text-green-800 
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        @if($p->status == 'dipinjam')
                                            <form action="{{ route('peminjam.peminjaman.kembalikan', $p->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengembalikan alat ini?')">
                                                @csrf
                                                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition">
                                                    <i class="fa-solid fa-rotate-left mr-1"></i> Kembalikan
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-gray-400 italic">Belum ada riwayat peminjaman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

</body>
</html>