<?php

namespace App\Services\Koordinator;

use App\Models\InternshipApplication;
use App\Models\Internship;
use App\Repositories\Koordinator\InternshipRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InternshipService
{
    protected $repo;

    public function __construct(InternshipRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getDetail($id)
    {
        return $this->repo->findWithDetails($id);
    }

    private function generateApplicationNumber()
    {
        $year = date('Y');

        $count = InternshipApplication::whereYear('created_at', $year)->count() + 1;
        $number = str_pad($count, 3, '0', STR_PAD_LEFT);

        return "{$number}/SATDIK-SMK/II.03/G.{$year}";
    }

    public function storeApplication($data, $action)
    {
        return DB::transaction(function () use ($data, $action) {
            $startDate = Carbon::now()->addDay();
            $endDate = $startDate->copy()->addDays(90);

            $status = ($action === 'pengajuan') ? 'menunggu_acc_pengajuan' : 'draft';

            $appNumber = isset($data['id'])
                ? InternshipApplication::find($data['id'])->application_number
                : $this->generateApplicationNumber();

            $application = InternshipApplication::updateOrCreate(
                ['id' => $data['id'] ?? null],
                [
                    'application_number' => $appNumber,
                    'coordinator_id' => Auth::id(),
                    'industry_id' => $data['industry_id'],
                    'pembimbing_id' => $data['pembimbing_id'],
                    'suggested_start_date' => $startDate,
                    'suggested_end_date' => $endDate,
                    'status' => $status,
                ]
            );

            $application->students()->sync($data['student_ids']);
            return $application;
        });
    }

    public function placementProcess($id, $data, $action)
    {
        return DB::transaction(function () use ($id, $data, $action) {
            $app = InternshipApplication::findOrFail($id);

            $start = Carbon::parse($data['departure_date']);
            if ($data['duration_option'] === 'custom') {
                $end = Carbon::parse($data['final_end_date']);
            } else {
                $months = $data['duration_option'] === '3 Bulan' ? 3 : 6;
                $end = $start->copy()->addMonths($months);
            }

            $newStatus = ($action === 'pengiriman') ? 'menunggu_acc_pengiriman' : 'pengiriman';

            $app->update([
                'departure_date' => $data['departure_date'],
                'duration_option' => $data['duration_option'],
                'final_end_date' => $end,
                'status' => $newStatus
            ]);

            return $app;
        });
    }
}
