<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Peminjam - Sistem Peminjaman Alat</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 font-sans flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between hidden md:flex shadow-lg">
        <div>
            <!-- Header Sidebar -->
            <div class="p-5 text-white font-bold text-lg tracking-wide border-b border-slate-800">
                PANEL PEMINJAM
            </div>
            <!-- Menu Navigasi -->
            <nav class="p-4 space-y-2 text-sm font-medium">
                <a href="{{ route('peminjam.katalog') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg bg-blue-600 text-white shadow-sm">
                    <i class="fa-solid fa-boxes-stacked"></i> Katalog & Pinjam Alat
                </a>
                <a href="{{ route('peminjam.riwayat') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition">
                    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat & Pengembalian
                </a>
            </nav>
        </div>
        <!-- Footer Sidebar (Logged in user) -->
        <div class="p-4 border-t border-slate-800 text-xs text-slate-400">
            Logged in as: <span class="font-bold text-white">{{ Auth::user()->name ?? 'Peminjam' }}</span>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col overflow-hidden">
        
        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shadow-sm">
            <h1 class="text-lg font-bold text-gray-800">Dashboard Peminjam</h1>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-1.5 rounded-lg text-sm font-semibold shadow-sm transition">
                    Logout
                </button>
            </form>
        </header>

        <!-- SCROLLABLE CONTENT -->
        <main class="flex-1 overflow-y-auto p-6 space-y-6">

            <!-- NOTIFIKASI FLASH MESSAGE -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl text-sm flex items-center justify-between shadow-sm">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="font-bold">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-sm flex items-center justify-between shadow-sm">
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="font-bold">&times;</button>
                </div>
            @endif

            <!-- WELCOME BANNER -->
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-xl text-sm font-medium shadow-sm">
                Selamat datang, <span class="font-bold">{{ Auth::user()->name ?? 'Peminjam' }}</span>! Anda login sebagai hak akses <span class="font-bold underline">PEMINJAM</span>.
            </div>

            <!-- BAGIAN KATALOG ALAT -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b pb-4">
                    <div>
                        <h2 class="text-md font-bold text-gray-800">Daftar Katalog Alat Tersedia</h2>
                        <p class="text-xs text-gray-500">Pilih alat yang ingin Anda pinjam beserta tanggal rencana pengembalian.</p>
                    </div>
                    <!-- Form Search -->
                    <form action="{{ route('peminjam.katalog') }}" method="GET" class="flex gap-2 w-full sm:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama alat..." class="border rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-60">
                        <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-3 py-1.5 rounded-lg text-sm font-semibold">Cari</button>
                    </form>
                </div>

                <!-- FORM PENGAJUAN PEMINJAMAN BANYAK ALAT (MULTIPLE) -->
                <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Rencana Tanggal Pengembalian <span class="text-red-500">*</span></label>
                        <input type="date" name="tgl_kembali_plan" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="border rounded-lg px-3 py-2 text-sm w-full sm:w-72 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- TABEL KATALOG -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600 border-collapse">
                            <thead class="bg-gray-50 text-gray-700 uppercase text-xs border-b">
                                <tr>
                                    <th class="p-3 text-center w-12">Pilih</th>
                                    <th class="p-3">Nama Alat</th>
                                    <th class="p-3">Kategori</th>
                                    <th class="p-3">Sisa Stok</th>
                                    <th class="p-3 w-32">Jumlah Pinjam</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($alats as $alat)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-3 text-center">
                                            <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}" class="rounded text-blue-600 focus:ring-blue-500 w-4 h-4">
                                        </td>
                                        <td class="p-3 font-semibold text-gray-800">{{ $alat->nama_alat }}</td>
                                        <td class="p-3 text-xs">
                                            <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded font-medium">{{ $alat->kategori->nama_kategori ?? '-' }}</span>
                                        </td>
                                        <td class="p-3 font-bold text-blue-600">{{ $alat->stok }} unit</td>
                                        <td class="p-3">
                                            <input type="number" name="jumlah[]" value="1" min="1" max="{{ $alat->stok }}" class="border rounded-lg px-2 py-1 text-sm w-24 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-6 text-center text-gray-400 italic">Tidak ada alat yang tersedia saat ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(count($alats) > 0)
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-semibold shadow-sm transition flex items-center gap-2">
                                <i class="fa-solid fa-paper-plane"></i> Ajukan Peminjaman Terpilih
                            </button>
                        </div>
                    @endif
                </form>
            </div>

        </main>
    </div>

</body>
</html>