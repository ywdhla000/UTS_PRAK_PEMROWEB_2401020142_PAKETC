<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pesanan Laundry</title>
</head>
<body>

    <h1>Edit Pesanan Laundry</h1>
    <a href="{{ route('laundry.index') }}">Kembali ke Daftar Pesanan</a>

    @if(session('error'))
        <p><strong>KESALAHAN:</strong> {{ session('error') }}</p>
    @endif

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

    <form action="{{ route('laundry.update', $pesanan['id']) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label for="kode_pesanan">Kode Pesanan:</label><br>
            <input type="text" id="kode_pesanan" name="kode_pesanan" value="{{ old('kode_pesanan', $pesanan['kode_pesanan']) }}" maxlength="10">
        </p>
        <p>
            <label for="nama_pelanggan">Nama Pelanggan:</label><br>
            <input type="text" id="nama_pelanggan" name="nama_pelanggan" value="{{ old('nama_pelanggan', $pesanan['nama_pelanggan']) }}">
        </p>
        <p>
            <label for="nomor_telepon">Nomor Telepon:</label><br>
            <input type="text" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon', $pesanan['nomor_telepon']) }}">
        </p>
        <p>
            <label for="berat_kg">Berat (kg) [1 - 20]:</label><br>
            <input type="number" id="berat_kg" name="berat_kg" value="{{ old('berat_kg', $pesanan['berat_kg']) }}" min="1" max="20">
        </p>
        <p>
            <label for="jenis_layanan_id">Jenis Layanan:</label><br>
            <select id="jenis_layanan_id" name="jenis_layanan_id">
                @foreach($layananList as $layanan)
                    <option value="{{ $layanan['id'] }}" {{ old('jenis_layanan_id', $pesanan['jenis_layanan_id']) == $layanan['id'] ? 'selected' : '' }}>
                        {{ $layanan['nama_layanan'] }}
                    </option>
                @endforeach
            </select>
        </p>
        <button type="submit">Perbarui Pesanan</button>
    </form>

</body>
</html>