<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Internship;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CompleteInternships extends Command
{
    protected $signature = 'internship:complete';
    protected $description = 'Memperbarui status PKL secara otomatis menjadi selesai jika sudah melewati tanggal selesai (end_date)';

    public function handle()
    {
        $today = Carbon::today()->toDateString();

        $internships = Internship::where('status', 'aktif')
            ->whereDate('end_date', '<=', $today)
            ->get();

        if ($internships->isEmpty()) {
            $this->info("Tidak ada siswa yang masa PKL-nya berakhir hari ini.");
            return;
        }

        DB::transaction(function () use ($internships) {
            $studentIds = [];
            $internshipIds = [];

            foreach ($internships as $intern) {
                $internshipIds[] = $intern->id;
                $studentIds[] = $intern->student_id;
            }

            Internship::whereIn('id', $internshipIds)->update(['status' => 'selesai']);

            Student::whereIn('id', $studentIds)->update(['is_pkl' => false]);
        });

        $this->info("Berhasil memperbarui status " . $internships->count() . " siswa PKL menjadi selesai.");
    }
}
