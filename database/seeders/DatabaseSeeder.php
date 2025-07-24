<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Position;
use App\Models\PositionUser;
use App\Models\Report;
use App\Models\ReportDisposition;
use App\Models\ReportImage; // Import ReportImage model
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedPositions();
        $this->seedPositionUsers();
        $this->seedReports();
        $this->seedReportDispositions();
        $this->seedReportImages(); // Add call to seedReportImages
    }

    private function seedUsers()
    {
        User::updateOrCreate([
            'email' => 'fauzan@gmail.com',
        ], [
            'name' => 'Superadmin',
            'email' => 'fauzan@gmail.com',
            'role' => 'superadmin',
            'password' => 'password',
        ]);

        User::updateOrCreate([
            'email' => 'andi@example.com',
        ], [
            'name' => 'Andi Pratama',
            'email' => 'andi@example.com',
            'role' => 'user',
            'password' => Hash::make('password'),
        ]);

        // Kadus
        User::updateOrCreate([
            'email' => 'budi@example.com',
        ], [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'role' => 'user',
            'password' => Hash::make('password'),
        ]);

        User::updateOrCreate([
            'email' => 'citra@example.com',
        ], [
            'name' => 'Citra Dewi',
            'email' => 'citra@example.com',
            'role' => 'user',
            'password' => Hash::make('password'),
        ]);

        User::updateOrCreate([
            'email' => 'dedi@example.com',
        ], [
            'name' => 'Dedi Kurniawan',
            'email' => 'dedi@example.com',
            'role' => 'user',
            'password' => Hash::make('password'),
        ]);

        // RT
        User::updateOrCreate([
            'email' => 'eko@example.com',
        ], [
            'name' => 'Eko Wijaya',
            'email' => 'eko@example.com',
            'role' => 'user',
            'password' => Hash::make('password'),
        ]);

        User::updateOrCreate([
            'email' => 'fina@example.com',
        ], [
            'name' => 'Fina Maharani',
            'email' => 'fina@example.com',
            'role' => 'user',
            'password' => Hash::make('password'),
        ]);

        User::updateOrCreate([
            'email' => 'gilang@example.com',
        ], [
            'name' => 'Gilang Ramadhan',
            'email' => 'gilang@example.com',
            'role' => 'user',
            'password' => Hash::make('password'),
        ]);

        User::updateOrCreate([
            'email' => 'benny@rectmedia.id',
        ], [
            'name' => 'Benny',
            'email' => 'benny@rectmedia.id',
            'role' => 'user',
            'password' => Hash::make('password'),
        ]);
    }

    private function seedPositions()
    {
        // Kepala Desa (level paling atas, parent_id = null)
        $kepalaDesa = Position::updateOrCreate([
            'name' => 'Kepala Desa',
            'detail' => 'Desa Sukamaju',
            'parent_id' => null,
        ]);

        // Kadus dengan parent Kepala Desa
        $kadusA = Position::updateOrCreate([
            'name' => 'Kadus A',
            'detail' => 'Desa Sukamaju',
            'parent_id' => $kepalaDesa->id,
        ]);

        $kadusB = Position::updateOrCreate([
            'name' => 'Kadus B',
            'detail' => 'Desa Sukamaju',
            'parent_id' => $kepalaDesa->id,
        ]);

        // RT dengan parent Kadus A
        Position::updateOrCreate([
            'name' => 'RT 1',
            'detail' => 'Desa Sukamaju',
            'parent_id' => $kadusA->id,
        ]);

        Position::updateOrCreate([
            'name' => 'RT 2',
            'detail' => 'Desa Sukamaju',
            'parent_id' => $kadusA->id,
        ]);

        // RT dengan parent Kadus B
        Position::updateOrCreate([
            'name' => 'RT 3',
            'detail' => 'Desa Sukamaju',
            'parent_id' => $kadusA->id,
        ]);

        // RT dengan parent Kadus B
        Position::updateOrCreate([
            'name' => 'RT 1',
            'detail' => 'Desa Sukamaju, Kadus B',
            'parent_id' => $kadusB->id,
        ]);

        Position::updateOrCreate([
            'name' => 'RT 2',
            'detail' => 'Desa Sukamaju, Kadus B',
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

        // $budi->positions()->syncWithoutDetaching([$kadusA->id]);   // Budi Kadus A
        // $citra->positions()->syncWithoutDetaching([$kadusB->id]);  // Citra Kadus B
        // $dedi->positions()->syncWithoutDetaching([$kepalaDesa->id]); // Dedi Kepala Desa (misal)
        // $eko->positions()->syncWithoutDetaching([$rt1->id]);       // Eko RT 1
        // $fina->positions()->syncWithoutDetaching([$rt2->id]);      // Fina RT 2
        // $gilang->positions()->syncWithoutDetaching([$rt3->id]);    // Gilang RT 3
        
        $budi->update(['position_id' => $kadusA->id]);
        $citra->update(['position_id' => $kadusB->id]);
        $dedi->update(['position_id' => $kepalaDesa->id]); // Dedi Kepala Desa (misal)
        $eko->update(['position_id' => $rt1->id]);
        $fina->update(['position_id' => $rt2->id]);
        $gilang->update(['position_id' => $rt3->id]);


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
            'status' => Report::SUBMITTED,
        ]);

        Report::updateOrCreate([
            'title' => 'Lampu Jalan Mati',
        ], [
            'user_id' => $andi->id,
            'content' => 'Lampu jalan di dekat pos RW mati sudah 2 minggu, area gelap dan rawan kecelakaan.',
            'status' => Report::SUBMITTED,
        ]);

        Report::updateOrCreate([
            'title' => 'Sampah Menumpuk di RT 2',
        ], [
            'user_id' => $andi->id,
            'content' => 'Sampah di RT 2 sudah menumpuk selama seminggu, mohon segera diangkut.',
            'status' => Report::SUBMITTED,
        ]);

        Report::updateOrCreate([
            'title' => 'Pohon Tumbang di RT 3',
        ], [
            'user_id' => $andi->id,
            'content' => 'Ada pohon tumbang di RT 3 yang menghalangi jalan utama.',
            'status' => Report::SUBMITTED,
        ]);

        Report::updateOrCreate([
            'title' => 'Jalan Rusak di RT 1',
        ], [
            'user_id' => $andi->id,
            'content' => 'Jalan di RT 1 sangat rusak dan berlubang, membahayakan warga terutama saat hujan.',
            'status' => Report::SUBMITTED,
        ]);


    }

    private function seedReportDispositions()
    {
        // Make Delay to ensure all models are created
        sleep(1);
        $kepalaDesa = Position::where('name', 'Kepala Desa')->first();
        $kadusA = Position::where('name', 'Kadus A')->first();
        $kadusB = Position::where('name', 'Kadus B')->first();
        $rt2 = Position::where('name', 'RT 2')->first();
        $rt3 = Position::where('name', 'RT 3')->first();

        // Disposisi laporan dari Kadus A ke Kepala Desa
        $report1 = Report::where('title', 'Jalan Rusak di RT 1')->first();
        $dispo1 = ReportDisposition::updateOrCreate([
            'report_id' => $report1->id,
            'from_position_id' => null,
            'to_position_id' => $kepalaDesa->id,
        ], [
            'note' => 'Saya teruskan ke Kepala Desa, mohon diperhatikan.',
        ]);

        // Write log
        // $this->command->line("Disposisi laporan: {$dispo1->id} {$report1->title} dari Kadus A ke Kepala Desa");

        $report1->createStatusLog(1, Report::PENDING, 'Laporan diteruskan ke Kepala Desa', null, $dispo1->id);
        $report1->update(['status' => Report::PENDING]);

        // Disposisi laporan dari Kadus B ke Kepala Desa
        $report2 = Report::where('title', 'Lampu Jalan Mati')->first();
        $dispo2 = ReportDisposition::updateOrCreate([
            'report_id' => $report2->id,
            'from_position_id' => null,
            'to_position_id' => $kepalaDesa->id,
        ], [
            'note' => 'Lampu jalan mati perlu diperbaiki secepatnya.',
        ]);
        $report2->createStatusLog(1, Report::PENDING, 'Laporan diteruskan ke Kepala Desa', null, $dispo2->id);
        $report2->update(['status' => Report::PENDING]);



        // Disposisi laporan baru
        // $report3 = Report::where('title', 'Sampah Menumpuk di RT 2')->first();
        // ReportDisposition::updateOrCreate([
        //     'report_id' => $report3->id,
        //     'from_position_id' => $rt2->id,
        //     'to_position_id' => $kadusA->id,
        // ], [
        //     'note' => 'Mohon segera ditangani, sampah menumpuk di RT 2.',
        // ]);
        // $report3->update(['status' => Report::PENDING]);


        // $report4 = Report::where('title', 'Pohon Tumbang di RT 3')->first(); 
        // ReportDisposition::updateOrCreate([
        //     'report_id' => $report4->id,
        //     'from_position_id' => $rt3->id,
        //     'to_position_id' => $kadusB->id,
        // ], [
        //     'note' => 'Pohon tumbang di RT 3 perlu segera dibersihkan.',
        // ]);
        // $report4->update(['status' => Report::PENDING]);
    }

    private function seedReportImages()
    {
        $reports = Report::all();

        foreach ($reports as $report) {
            // 1. Buat record kosong dulu untuk dapatkan id
            $reportImage1 = new ReportImage();
            $reportImage1->report_id = $report->id;
            $reportImage1->created_by = $report->user_id ?? 1;
            $reportImage1->is_temporary = false;
            $reportImage1->save();

            $reportImage2 = new ReportImage();
            $reportImage2->report_id = $report->id;
            $reportImage2->created_by = $report->user_id ?? 1;
            $reportImage2->is_temporary = false;
            $reportImage2->save();

            // 2. Generate nama file: {id}_{timestamp}_{random}.jpg
            $id1 = $reportImage1->id;
            $id2 = $reportImage2->id;
            $timestamp = now()->timestamp;
            $random1 = mt_rand(100000, 999999);
            $random2 = mt_rand(100000, 999999);

            $filename1 = "{$id1}_{$timestamp}_{$random1}.jpg";
            $filename2 = "{$id2}_{$timestamp}_{$random2}.jpg";

            // 3. Salin gambar nyata ke storage
            $imagePath1 = "images/reports/{$filename1}";
            $imagePath2 = "images/reports/{$filename2}";

            Storage::disk('public')->put($imagePath1, file_get_contents(base_path('resources/images/sample1.jpg')));
            Storage::disk('public')->put($imagePath2, file_get_contents(base_path('resources/images/sample2.jpg')));

            // 4. Update path di database
            $reportImage1->image_path = $imagePath1;
            $reportImage1->save();

            $reportImage2->image_path = $imagePath2;
            $reportImage2->save();
        }
    }
}
