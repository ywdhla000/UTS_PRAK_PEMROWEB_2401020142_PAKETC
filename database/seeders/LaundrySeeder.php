<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaundrySeeder extends Seeder
{
    public function run(): void
    {
        $cuciKeringId = DB::table('jenis_layanan')->insertGetId([
            'nama_layanan' => 'Cuci Kering',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cuciSetrikaId = DB::table('jenis_layanan')->insertGetId([
            'nama_layanan' => 'Cuci Setrika',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pesanan_laundry')->insert([
            [
                'kode_pesanan' => 'LD00000001',
                'nama_pelanggan' => 'Ayu Lestari',
                'nomor_telepon' => '081200000001',
                'berat_kg' => 5,
                'jenis_layanan_id' => $cuciKeringId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_pesanan' => 'LD00000002',
                'nama_pelanggan' => 'Rafi Akbar',
                'nomor_telepon' => '081200000002',
                'berat_kg' => 8,
                'jenis_layanan_id' => $cuciSetrikaId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_pesanan' => 'LD00000003',
                'nama_pelanggan' => 'Nina Putri',
                'nomor_telepon' => '081200000003',
                'berat_kg' => 3,
                'jenis_layanan_id' => $cuciKeringId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}