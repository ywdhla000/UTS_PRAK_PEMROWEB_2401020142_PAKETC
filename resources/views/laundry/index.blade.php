<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Pesanan Laundry - Paket C</title>
</head>
<body>

    <h1>Sistem Pengelolaan Pesanan Laundry</h1>

    {{-- Feedback Operasi --}}
    @if(session('success'))
        <p><strong>BERHASIL:</strong> {{ session('success') }}</p>
    @endif
    @if(session('error'))
        <p><strong>KESALAHAN:</strong> {{ session('error') }}</p>
    @endif

    {{-- Error Validasi Form --}}
    @if($errors->any())
        <div>
            <strong>Terjadi kesalahan input:</strong>
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <hr>

    {{-- Form Tambah Pesanan --}}
    <h2>Tambah Pesanan Laundry Baru</h2>
    <form action="{{ route('laundry.store') }}" method="POST">
        @csrf
        <p>
            <label for="kode_pesanan">Kode Pesanan (10 Karakter):</label><br>
            <input type="text" id="kode_pesanan" name="kode_pesanan" value="{{ old('kode_pesanan') }}" maxlength="10">
        </p>
        <p>
            <label for="nama_pelanggan">Nama Pelanggan:</label><br>
            <input type="text" id="nama_pelanggan" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}">
        </p>
        <p>
            <label for="nomor_telepon">Nomor Telepon:</label><br>
            <input type="text" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}">
        </p>
        <p>
            <label for="berat_kg">Berat (kg) [1 - 20]:</label><br>
            <input type="number" id="berat_kg" name="berat_kg" value="{{ old('berat_kg') }}" min="1" max="20">
        </p>
        <p>
            <label for="jenis_layanan_id">Jenis Layanan:</label><br>
            <select id="jenis_layanan_id" name="jenis_layanan_id">
                <option value="">-- Pilih Layanan --</option>
                @foreach($layananList as $layanan)
                    <option value="{{ $layanan['id'] }}" {{ old('jenis_layanan_id') == $layanan['id'] ? 'selected' : '' }}>
                        {{ $layanan['nama_layanan'] }}
                    </option>
                @endforeach
            </select>
        </p>
        <button type="submit">Simpan Pesanan</button>
    </form>

    <hr>

    {{-- Form Pencarian dan Filter --}}
    <h2>Pencarian dan Filter</h2>
    <form action="{{ route('laundry.index') }}" method="GET">
        <label for="search">Cari (Kode, Nama, Telepon):</label>
        <input type="text" id="search" name="search" value="{{ $search }}">

        <label for="filter_layanan">Filter Layanan:</label>
        <select id="filter_layanan" name="jenis_layanan_id">
            <option value="">Semua Layanan</option>
            @foreach($layananList as $layanan)
                <option value="{{ $layanan['id'] }}" {{ $filterLayanan == $layanan['id'] ? 'selected' : '' }}>
                    {{ $layanan['nama_layanan'] }}
                </option>
            @endforeach
        </select>

        <button type="submit">Terapkan</button>
        <a href="{{ route('laundry.index') }}">Reset</a>
    </form>

    <br>

    {{-- Tabel Data Pesanan --}}
    <h2>Daftar Pesanan Laundry</h2>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Pesanan</th>
                <th>Nama Pelanggan</th>
                <th>Nomor Telepon</th>
                <th>Berat (kg)</th>
                <th>Status Berat</th>
                <th>Layanan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesananList as $index => $row)
                @php
                    $kategoriBerat = \App\Http\Controllers\PesananLaundryController::statusBerat((int)$row['berat_kg']);
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['kode_pesanan'] }}</td>
                    <td>{{ $row['nama_pelanggan'] }}</td>
                    <td>{{ $row['nomor_telepon'] }}</td>
                    <td>{{ $row['berat_kg'] }}</td>
                    <td>
                        {{-- Percabangan Blade sesuai modul 2 --}}
                        @if($kategoriBerat === 'Besar')
                            <strong>Besar</strong>
                        @else
                            Reguler
                        @endif
                    </td>
                    <td>{{ $row['nama_layanan'] }}</td>
                    <td>
                        <a href="{{ route('laundry.edit', $row['id']) }}">Edit</a>
                        
                        <form action="{{ route('laundry.destroy', $row['id']) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus pesanan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Tidak ada data pesanan yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>