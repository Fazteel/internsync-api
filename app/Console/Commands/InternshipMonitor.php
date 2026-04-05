<?php

namespace App\Console\Commands;

use App\Models\Internship;
use App\Models\Notification;
use App\Models\Permission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InternshipMonitor extends Command
{
    protected $signature = 'internship:monitor';
    protected $description = 'Sistem pemantauan otomatis untuk memonitor progres PKL';

    public function handle()
    {
        $this->info('Memulai proses pemantauan PKL...');

        $activeInterns = Internship::where('status', 'active')->with(['student.user', 'pembimbing', 'industry'])->get();

        foreach ($activeInterns as $intern) {
            $lastLog = $intern->logbooks()->latest('date')->first();
            $startDate = $lastLog ? Carbon::parse($lastLog->date)->addDay() : Carbon::parse($intern->start_date);
            $endDate = now();

            $workDaysMissing = $startDate->diffInWeekdays($endDate);
            $approvedPermissions = Permission::where('internship_id', $intern->id)
                ->where('status', 'approved')
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->count();

            $netMangkir = max(0, $workDaysMissing - $approvedPermissions);

            if ($netMangkir >= 3) {
                $this->sendWarning(
                    $intern,
                    'Peringatan Mangkir Logbook',
                    "Yth. {$intern->student->user->name}, Anda terdeteksi mangkir (tidak mengisi logbook & tidak ada izin) selama {$netMangkir} hari kerja. Segera lengkapi laporan Anda!"
                );
            }

            if (Carbon::parse($intern->end_date)->isSameDay(now()->addDays(7))) {
                Notification::send(
                    $intern->pembimbing_id,
                    'Pengingat Evaluasi PKL',
                    "Siswa {$intern->student->user->name} akan menyelesaikan program PKL dalam 7 hari. Mohon mempersiapkan proses evaluasi dan penilaian.",
                    'info'
                );
            }

            $lastVisit = DB::table('tr_industry_visits')
                ->where('internship_id', $intern->id)
                ->latest('visit_date')
                ->first();

            if (!$lastVisit || Carbon::parse($lastVisit->visit_date)->diffInDays(now()) >= 30) {
                Notification::send(
                    $intern->pembimbing_id,
                    'Pengingat Kunjungan Industri',
                    "Sudah lebih dari 30 hari sejak kunjungan terakhir ke siswa {$intern->student->user->name} di {$intern->industry->name}. Mohon menjadwalkan kunjungan pembimbingan.",
                    'warning'
                );
            }
        }

        $this->checkHubinTasks();

        DB::table('sys_logs')->where('created_at', '<', now()->subDays(30))->delete();

        $this->info('Pembersihan log sistem selesai.');
    }

    protected function checkHubinTasks()
    {
        $hubinUsers = User::whereHas('roles', fn($q) => $q->where('name', 'hubin'))->pluck('id');

        $pendingDepartures = Internship::where('status', 'pending')
            ->whereDate('start_date', '<=', now()->addDays(3))
            ->count();

        if ($pendingDepartures > 0) {
            foreach ($hubinUsers as $id) {
                Notification::send(
                    $id,
                    'Pengingat Verifikasi Keberangkatan',
                    "Terdapat {$pendingDepartures} siswa yang dijadwalkan berangkat PKL dalam 3 hari ke depan namun belum diverifikasi. Mohon segera melakukan verifikasi.",
                    'error'
                );
            }
        }
    }

    protected function sendWarning($intern, $title, $msg)
    {
        Notification::send($intern->student->user_id, $title, $msg, 'error');

        if ($intern->pembimbing_id) {
            Notification::send(
                $intern->pembimbing_id,
                'Pengingat Pengisian Logbook Siswa',
                "Siswa bimbingan Anda, {$intern->student->user->name}, belum memperbarui logbook kegiatan selama 3 hari.",
                'warning'
            );
        }
    }
}
