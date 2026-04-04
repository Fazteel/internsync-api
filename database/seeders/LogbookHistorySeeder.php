<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class LogbookHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $internshipId = 4;
        $nis = '313852189011';

        $start = Carbon::parse('2026-01-31');
        $end = Carbon::parse('2026-03-29');

        $period = CarbonPeriod::create($start, $end);

        $activities = [
            'Mempelajari arsitektur sistem',
            'Slicing UI Dashboard',
            'Fixing bug di modul auth',
            'Diskusi progress dengan tim IT',
            'Optimasi query database',
            'Maintenance server lokal',
            'Membuat dokumentasi API',
            'Testing fitur baru'
        ];

        $statuses = ['approved', 'submitted', 'revised'];

        foreach ($period as $date) {
            if ($date->isSunday()) {
                continue;
            }

            $dateFormatted = $date->format('Y-m-d');
            $fileSuffix = $date->format('Ymd');

            DB::table('tr_logbooks')->insert([
                'internship_id' => $internshipId,
                'date' => $dateFormatted,
                'activity' => $activities[array_rand($activities)],
                'file_path' => "logbooks/Logbook_{$nis}_{$fileSuffix}.png",
                'status' => $statuses[array_rand($statuses)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Data logbook buat internship_id 4 berhasil disuntik!');
    }
}