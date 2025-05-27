<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Position;
use App\Models\PositionUser;
use App\Models\Report;
use App\Models\ReportDisposition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedPositions();
        $this->seedPositionUsers();
        $this->seedReports();
        $this->seedReportDispositions();
    }

    private function seedUsers()
    {
        User::updateOrCreate([
            'email' => 'fauzan@gmail.com',
        ], [
            'name' => 'Superadmin',
            'email' => 'fauzan@gmail.com',
            'role' => 'superadmin',
            'password' => 'fauzan99',
        ]);

        User::updateOrCreate([
            'email' => 'andi@example.com',
        ], [
            'name' => 'Andi Pratama',
            'email' => 'andi@example.com',
            'role' => 'user',
            'password' => Hash::make('passwordandi'),
        ]);

        // Kadus
        User::updateOrCreate([
            'email' => 'budi@example.com',
        ], [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'role' => 'user',
            'password' => Hash::make('passwordbudi'),
        ]);

        User::updateOrCreate([
            'email' => 'citra@example.com',
        ], [
            'name' => 'Citra Dewi',
            'email' => 'citra@example.com',
            'role' => 'user',
            'password' => Hash::make('passwordcitra'),
        ]);

        User::updateOrCreate([
            'email' => 'dedi@example.com',
        ], [
            'name' => 'Dedi Kurniawan',
            'email' => 'dedi@example.com',
            'role' => 'user',
            'password' => Hash::make('passworddedi'),
        ]);

        // RT
        User::updateOrCreate([
            'email' => 'eko@example.com',
        ], [
            'name' => 'Eko Wijaya',
            'email' => 'eko@example.com',
            'role' => 'user',
            'password' => Hash::make('passwordeko'),
        ]);

        User::updateOrCreate([
            'email' => 'fina@example.com',
        ], [
            'name' => 'Fina Maharani',
            'email' => 'fina@example.com',
            'role' => 'user',
            'password' => Hash::make('passwordfina'),
        ]);

        User::updateOrCreate([
            'email' => 'gilang@example.com',
        ], [
            'name' => 'Gilang Ramadhan',
            'email' => 'gilang@example.com',
            'role' => 'user',
            'password' => Hash::make('passwordgilang'),
        ]);
    }

    private function seedPositions()
    {
        // Kepala Desa (level paling atas, parent_id = null)
        $kepalaDesa = Position::updateOrCreate([
            'name' => 'Kepala Desa',
            'village_name' => 'Desa Sukamaju',
            'parent_id' => null,
        ]);

        // Kadus dengan parent Kepala Desa
        $kadusA = Position::updateOrCreate([
            'name' => 'Kadus A',
            'village_name' => 'Desa Sukamaju',
            'parent_id' => $kepalaDesa->id,
        ]);

        $kadusB = Position::updateOrCreate([
            'name' => 'Kadus B',
            'village_name' => 'Desa Sukamaju',
            'parent_id' => $kepalaDesa->id,
        ]);

        // RT dengan parent Kadus A
        Position::updateOrCreate([
            'name' => 'RT 1',
            'village_name' => 'Desa Sukamaju',
            'parent_id' => $kadusA->id,
        ]);

        Position::updateOrCreate([
            'name' => 'RT 2',
            'village_name' => 'Desa Sukamaju',
            'parent_id' => $kadusA->id,
        ]);

        // RT dengan parent Kadus B
        Position::updateOrCreate([
            'name' => 'RT 3',
            'village_name' => 'Desa Sukamaju',
            'parent_id' => $kadusB->id,
        ]);
    }


    private function seedPositionUsers()
    {
        // Ambil posisi dulu
        $kepalaDesa = Position::where('name', 'Kepala Desa')->first();
        $kadusA = Position::where('name', 'Kadus A')->first();
        $kadusB = Position::where('name', 'Kadus B')->first();
        $rt1 = Position::where('name', 'RT 1')->first();
        $rt2 = Position::where('name', 'RT 2')->first();
        $rt3 = Position::where('name', 'RT 3')->first();

        // Ambil user
        $budi = User::where('email', 'budi@example.com')->first();     // Kadus
        $citra = User::where('email', 'citra@example.com')->first();   // Kadus
        $dedi = User::where('email', 'dedi@example.com')->first();     // Kadus
        $eko = User::where('email', 'eko@example.com')->first();       // RT
        $fina = User::where('email', 'fina@example.com')->first();     // RT
        $gilang = User::where('email', 'gilang@example.com')->first(); // RT
        $andi = User::where('email', 'andi@example.com')->first();     // warga biasa
        $superadmin = User::where('email', 'fauzan@gmail.com')->first();

        // Assign posisi ke user (kadang user bisa punya posisi lebih dari 1, contoh)
        // Kalau kamu pakai pivot table 'position_user' dengan fields user_id, position_id

        $budi->positions()->syncWithoutDetaching([$kadusA->id]);   // Budi Kadus A
        $citra->positions()->syncWithoutDetaching([$kadusB->id]);  // Citra Kadus B
        $dedi->positions()->syncWithoutDetaching([$kepalaDesa->id]); // Dedi Kepala Desa (misal)
        $eko->positions()->syncWithoutDetaching([$rt1->id]);       // Eko RT 1
        $fina->positions()->syncWithoutDetaching([$rt2->id]);      // Fina RT 2
        $gilang->positions()->syncWithoutDetaching([$rt3->id]);    // Gilang RT 3

        // Superadmin gak punya posisi (optional)
    }

    private function seedReports()
    {
        $andi = User::where('email', 'andi@example.com')->first();

        // Buat 2 laporan contoh
        Report::updateOrCreate([
            'title' => 'Jalan Rusak di RT 1',
        ], [
            'user_id' => $andi->id,
            'content' => 'Jalan di RT 1 sangat rusak dan berlubang, membahayakan warga terutama saat hujan.',
            'status' => 'pending',
        ]);

        Report::updateOrCreate([
            'title' => 'Lampu Jalan Mati',
        ], [
            'user_id' => $andi->id,
            'content' => 'Lampu jalan di dekat pos RW mati sudah 2 minggu, area gelap dan rawan kecelakaan.',
            'status' => 'pending',
        ]);

        Report::updateOrCreate([
            'title' => 'Sampah Menumpuk di RT 2',
        ], [
            'user_id' => $andi->id,
            'content' => 'Sampah di RT 2 sudah menumpuk selama seminggu, mohon segera diangkut.',
            'status' => 'pending',
        ]);

        Report::updateOrCreate([
            'title' => 'Pohon Tumbang di RT 3',
        ], [
            'user_id' => $andi->id,
            'content' => 'Ada pohon tumbang di RT 3 yang menghalangi jalan utama.',
            'status' => 'pending',
        ]);
    }

    private function seedReportDispositions()
    {
        $kepalaDesa = Position::where('name', 'Kepala Desa')->first();
        $kadusA = Position::where('name', 'Kadus A')->first();
        $kadusB = Position::where('name', 'Kadus B')->first();
        $rt2 = Position::where('name', 'RT 2')->first();
        $rt3 = Position::where('name', 'RT 3')->first();

        $report1 = Report::where('title', 'Jalan Rusak di RT 1')->first();
        $report2 = Report::where('title', 'Lampu Jalan Mati')->first();

        // Disposisi laporan dari Kadus A ke Kepala Desa
        ReportDisposition::updateOrCreate([
            'report_id' => $report1->id,
            'from_position_id' => $kadusA->id,
            'to_position_id' => $kepalaDesa->id,
        ], [
            'note' => 'Saya teruskan ke Kepala Desa, mohon diperhatikan.',
        ]);

        // Disposisi laporan dari Kadus B ke Kepala Desa
        ReportDisposition::updateOrCreate([
            'report_id' => $report2->id,
            'from_position_id' => $kadusB->id,
            'to_position_id' => $kepalaDesa->id,
        ], [
            'note' => 'Lampu jalan mati perlu diperbaiki secepatnya.',
        ]);

        $report3 = Report::where('title', 'Sampah Menumpuk di RT 2')->first();
        // $report4 = Report::where('title', 'Pohon Tumbang di RT 3')->first();

        // Disposisi laporan baru
        ReportDisposition::updateOrCreate([
            'report_id' => $report3->id,
            'from_position_id' => $rt2->id,
            'to_position_id' => $kadusA->id,
        ], [
            'note' => 'Mohon segera ditangani, sampah menumpuk di RT 2.',
        ]);

        // ReportDisposition::updateOrCreate([
        //     'report_id' => $report4->id,
        //     'from_position_id' => $rt3->id,
        //     'to_position_id' => $kadusB->id,
        // ], [
        //     'note' => 'Pohon tumbang di RT 3 perlu segera dibersihkan.',
        // ]);
    }
}
