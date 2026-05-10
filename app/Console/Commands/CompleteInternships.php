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
    protected $description = 'Otomatis update status PKL jadi selesai kalau udah lewat end_date';

    public function handle()
    {
        $today = Carbon::today()->toDateString();

        $internships = Internship::where('status', 'aktif')
            ->whereDate('end_date', '<=', $today)
            ->get();

        if ($internships->isEmpty()) {
            $this->info("Kaga ada siswa yang PKL-nya kelar hari ini.");
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

        $this->info("Mantap! Berhasil update " . $internships->count() . " siswa PKL jadi selesai!");
    }
}
