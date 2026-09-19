@extends('layouts.app')

@section('title', 'Daftar Peminjaman - Master Admin')
@section('header-title', 'Manajemen Transaksi Peminjaman')

@section('content')
    @if (session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-bold text-gray-800">Daftar Transaksi Peminjaman</h3>

            <div class="flex items-center gap-3 w-full md:w-auto">
                {{-- Form Pencarian --}}
                <form action="{{ route('admin.peminjaman.index') }}" method="GET" class="flex flex-1 gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam / status..."
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                        Cari
                    </button>
                    @if (request('search'))
                        <a href="{{ route('admin.peminjaman.index') }}"
                            class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-3 text-sm rounded-lg flex items-center transition">
                            Reset
                        </a>
                    @endif
                </form>

                {{-- Tombol Tambah --}}
                <a href="{{ route('admin.peminjaman.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
                    + Tambah Peminjaman
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="px-4 py-3 border-b">Peminjam</th>
                        <th class="px-4 py-3 border-b">Detail Alat yang Dipinjam</th>
                        <th class="px-4 py-3 border-b">Tgl. Pinjam / Rencana Kembali</th>
                        <th class="px-4 py-3 border-b">Status</th>
                        <th class="px-4 py-3 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse ($peminjamans as $peminjaman)
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="px-4 py-3 border-b font-medium text-gray-900">
                                {{ $peminjaman->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 border-b">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($peminjaman->detailPeminjaman as $detail)
                                        <li>
                                            <span class="font-semibold">{{ $detail->alat->nama_alat }}</span> 
                                            <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">({{ $detail->jumlah }} unit)</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-4 py-3 border-b text-xs text-gray-600">
                                <span class="block">Dipinjam : {{ $peminjaman->tgl_pinjam }}</span>
                                <span class="block font-semibold text-red-600">Kembali: {{ $peminjaman->tgl_kembali_rencana }}</span>
                            </td>
                            <td class="px-4 py-3 border-b">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                    @if($peminjaman->status == 'menunggu') bg-yellow-100 text-yellow-800 
                                    @elseif($peminjaman->status == 'disetujui') bg-blue-100 text-blue-800 
                                    @elseif($peminjaman->status == 'ditolak') bg-red-100 text-red-800 
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($peminjaman->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 border-b">
                                <div class="flex flex-col gap-2">
                                    {{-- Form Ubah Status --}}
                                    <form action="{{ route('admin.peminjaman.updateStatus', $peminjaman->id) }}" method="POST" class="flex items-center gap-1">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none">
                                            <option value="menunggu" {{ $peminjaman->status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                            <option value="disetujui" {{ $peminjaman->status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                            <option value="dikembalikan" {{ $peminjaman->status == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                                            <option value="ditolak" {{ $peminjaman->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                        </select>
                                    </form>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('admin.peminjaman.destroy', $peminjaman->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data peminjaman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs font-semibold transition w-full">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">Belum ada data peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $peminjamans->links() }}
        </div>
    </div>
@endsection