@extends('layouts.app')

@section('title', 'Daftar Pengembalian Alat')
@section('header-title', 'Manajemen Transaksi Pengembalian')

@section('content')
<div class="space-y-6">

    {{-- Alert Notifikasi Sukses --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm flex items-center justify-between" role="alert">
            <span class="font-medium">{{ session('success') }}</span>
            <button type="button" class="text-green-700 hover:text-green-900" onclick="this.parentElement.remove();">&times;</button>
        </div>
    @endif

    <!-- Container Utama Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Header Card & Input Pencarian -->
        <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="text-lg font-bold text-gray-800">Daftar Pengembalian Alat</h2>

            <form action="{{ route('admin.pengembalian.index') }}" method="GET" class="flex gap-2">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari peminjam..." 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition shadow-sm">
                    Cari
                </button>
            </form>
        </div>

        <!-- Tabel Pengembalian -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">NO</th>
                        <th class="py-3 px-4">NAMA PEMINJAM</th>
                        <th class="py-3 px-4">ALAT YANG DIKEMBALIKAN</th>
                        <th class="py-3 px-4">TGL KEMBALI REALISASI</th>
                        <th class="py-3 px-4">KONDISI KEMBALI</th>
                        <th class="py-3 px-4">DENDA</th>
                        <th class="py-3 px-4">PETUGAS PENERIMA</th>
                        <th class="py-3 px-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse ($pengembalians as $index => $pengembalian)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-4 px-4 text-center font-medium text-gray-500">
                                {{ $pengembalians->firstItem() + $index }}
                            </td>
                            <td class="py-4 px-4 font-semibold text-gray-900">
                                {{ $pengembalian->peminjaman->user->name ?? 'User Tidak Ditemukan' }}
                            </td>
                            <td class="py-4 px-4">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($pengembalian->peminjaman->detailPeminjaman as $detail)
                                        <li>
                                            <span class="font-medium text-gray-800">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                            <span class="text-xs text-gray-500">({{ $detail->jumlah }} unit)</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($pengembalian->tgl_kembali)->translatedFormat('d F Y') }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $pengembalian->kondisi_kembali }}
                                </span>
                            </td>
                            <td class="py-4 px-4 font-medium whitespace-nowrap">
                                @if($pengembalian->denda > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                        Rp {{ number_format($pengembalian->denda, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                        Tidak Ada Denda
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-gray-600">
                                {{ $pengembalian->petugas->name ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <form action="{{ route('admin.pengembalian.destroy', $pengembalian->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pengembalian ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition shadow-sm">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400 font-medium">
                                Belum ada data transaksi pengembalian alat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination -->
        @if ($pengembalians->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                {{ $pengembalians->links() }}
            </div>
        @endif
    </div>
</div>
@endsection