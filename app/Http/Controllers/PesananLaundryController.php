<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use PDO;

class PesananLaundryController extends Controller
{
    public static function statusBerat(int $berat): string
    {
        return $berat >= 10 ? 'Besar' : 'Reguler';
    }

    public function index(Request $request)
    {
        try {
            /** @var PDO $pdo */
            $pdo = DB::connection()->getPdo();

            $stmtLayanan = $pdo->query("SELECT id, nama_layanan FROM jenis_layanan ORDER BY nama_layanan ASC");
            $layananList = $stmtLayanan->fetchAll(PDO::FETCH_ASSOC);

            $search = trim(strip_tags($request->query('search', '')));
            $filterLayanan = $request->query('jenis_layanan_id', '');

            $sql = "SELECT p.*, l.nama_layanan 
                    FROM pesanan_laundry p 
                    JOIN jenis_layanan l ON p.jenis_layanan_id = l.id 
                    WHERE 1=1";
            $bindings = [];

            if ($search !== '') {
                $sql .= " AND (p.kode_pesanan LIKE :s1 OR p.nama_pelanggan LIKE :s2 OR p.nomor_telepon LIKE :s3)";
                $bindings[':s1'] = "%{$search}%";
                $bindings[':s2'] = "%{$search}%";
                $bindings[':s3'] = "%{$search}%";
            }

            if ($filterLayanan !== '' && $filterLayanan !== null) {
                $sql .= " AND p.jenis_layanan_id = :filter_layanan";
                $bindings[':filter_layanan'] = $filterLayanan;
            }

            $sql .= " ORDER BY p.id DESC";

            $stmtPesanan = $pdo->prepare($sql);
            foreach ($bindings as $key => $val) {
                $stmtPesanan->bindValue($key, $val);
            }
            $stmtPesanan->execute();
            $pesananList = $stmtPesanan->fetchAll(PDO::FETCH_ASSOC);

            return view('laundry.index', compact('pesananList', 'layananList', 'search', 'filterLayanan'));
        } catch (Throwable $e) {
            report($e);
            return response('Terjadi kesalahan pada sistem atau basis data. Silakan coba beberapa saat lagi.', 500);
        }
    }

    public function store(Request $request)
    {
        $input = [
            'kode_pesanan'     => trim(strip_tags($request->input('kode_pesanan'))),
            'nama_pelanggan'   => trim(strip_tags($request->input('nama_pelanggan'))),
            'nomor_telepon'    => trim(strip_tags($request->input('nomor_telepon'))),
            'berat_kg'         => $request->input('berat_kg'),
            'jenis_layanan_id' => $request->input('jenis_layanan_id'),
        ];
        $request->merge($input);

        $request->validate([
            'kode_pesanan'     => 'required|alpha_num|size:10|unique:pesanan_laundry,kode_pesanan',
            'nama_pelanggan'   => 'required|string|min:3|max:100',
            'nomor_telepon'    => 'required|string|min:10|max:20|unique:pesanan_laundry,nomor_telepon',
            'berat_kg'         => 'required|integer|between:1,20',
            'jenis_layanan_id' => 'required|exists:jenis_layanan,id',
        ], [
            'kode_pesanan.size'         => 'Kode pesanan harus tepat 10 karakter alfanumerik.',
            'kode_pesanan.unique'       => 'Kode pesanan sudah terdaftar di sistem.',
            'nomor_telepon.unique'      => 'Nomor telepon sudah digunakan pada pesanan lain.',
            'berat_kg.between'          => 'Berat cucian harus antara 1 sampai 20 kg.',
            'jenis_layanan_id.exists'   => 'Layanan yang dipilih tidak valid.',
        ]);

        try {
            $pdo = DB::connection()->getPdo();
            $sql = "INSERT INTO pesanan_laundry (kode_pesanan, nama_pelanggan, nomor_telepon, berat_kg, jenis_layanan_id, created_at, updated_at) 
                    VALUES (:kode, :nama, :telp, :berat, :layanan_id, NOW(), NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':kode'       => $input['kode_pesanan'],
                ':nama'       => $input['nama_pelanggan'],
                ':telp'       => $input['nomor_telepon'],
                ':berat'      => (int)$input['berat_kg'],
                ':layanan_id' => (int)$input['jenis_layanan_id'],
            ]);

            return redirect()->route('laundry.index')->with('success', 'Pesanan laundry berhasil ditambahkan.');
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Gagal menyimpan pesanan. Silakan periksa kembali data Anda.');
        }
    }

    public function edit($id)
    {
        try {
            $pdo = DB::connection()->getPdo();

            $stmtPesanan = $pdo->prepare("SELECT * FROM pesanan_laundry WHERE id = :id");
            $stmtPesanan->execute([':id' => $id]);
            $pesanan = $stmtPesanan->fetch(PDO::FETCH_ASSOC);

            if (!$pesanan) {
                return redirect()->route('laundry.index')->with('error', 'Data pesanan tidak ditemukan.');
            }

            $stmtLayanan = $pdo->query("SELECT id, nama_layanan FROM jenis_layanan ORDER BY nama_layanan ASC");
            $layananList = $stmtLayanan->fetchAll(PDO::FETCH_ASSOC);

            return view('laundry.edit', compact('pesanan', 'layananList'));
        } catch (Throwable $e) {
            report($e);
            return redirect()->route('laundry.index')->with('error', 'Terjadi kesalahan saat memuat form edit.');
        }
    }

    public function update(Request $request, $id)
    {
        $input = [
            'kode_pesanan'     => trim(strip_tags($request->input('kode_pesanan'))),
            'nama_pelanggan'   => trim(strip_tags($request->input('nama_pelanggan'))),
            'nomor_telepon'    => trim(strip_tags($request->input('nomor_telepon'))),
            'berat_kg'         => $request->input('berat_kg'),
            'jenis_layanan_id' => $request->input('jenis_layanan_id'),
        ];
        $request->merge($input);

        $request->validate([
            'kode_pesanan'     => 'required|alpha_num|size:10|unique:pesanan_laundry,kode_pesanan,' . $id,
            'nama_pelanggan'   => 'required|string|min:3|max:100',
            'nomor_telepon'    => 'required|string|min:10|max:20|unique:pesanan_laundry,nomor_telepon,' . $id,
            'berat_kg'         => 'required|integer|between:1,20',
            'jenis_layanan_id' => 'required|exists:jenis_layanan,id',
        ], [
            'kode_pesanan.unique'  => 'Kode pesanan tersebut sudah digunakan oleh pesanan lain.',
            'nomor_telepon.unique' => 'Nomor telepon tersebut sudah digunakan oleh pesanan lain.',
        ]);

        try {
            $pdo = DB::connection()->getPdo();
            $sql = "UPDATE pesanan_laundry 
                    SET kode_pesanan = :kode, nama_pelanggan = :nama, nomor_telepon = :telp, 
                        berat_kg = :berat, jenis_layanan_id = :layanan_id, updated_at = NOW() 
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':kode'       => $input['kode_pesanan'],
                ':nama'       => $input['nama_pelanggan'],
                ':telp'       => $input['nomor_telepon'],
                ':berat'      => (int)$input['berat_kg'],
                ':layanan_id' => (int)$input['jenis_layanan_id'],
                ':id'         => $id,
            ]);

            return redirect()->route('laundry.index')->with('success', 'Data pesanan berhasil diperbarui.');
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Gagal memperbarui pesanan.');
        }
    }

    public function destroy($id)
    {
        try {
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare("DELETE FROM pesanan_laundry WHERE id = :id");
            $stmt->execute([':id' => $id]);

            return redirect()->route('laundry.index')->with('success', 'Pesanan laundry berhasil dihapus.');
        } catch (Throwable $e) {
            report($e);
            return redirect()->route('laundry.index')->with('error', 'Gagal menghapus data.');
        }
    }
}