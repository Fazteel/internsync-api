<?php

namespace App\Http\Controllers\Api\V1\Koordinator;

use App\Http\Controllers\Controller;
use App\Repositories\Koordinator\InternshipRepository;
use App\Services\Koordinator\InternshipService;
use Illuminate\Http\Request;

class InternshipController extends Controller
{
    protected $service, $repo;

    public function __construct(InternshipService $service, InternshipRepository $repo)
    {
        $this->service = $service;
        $this->repo = $repo;
    }

    public function listApplications()
    {
        return response()->json($this->repo->getApplicationsByStatus([
            'draft',
            'menunggu_acc_pengajuan',
            'pengajuan',
            'ditolak'
        ]));
    }

    public function listPlacements()
    {
        return response()->json($this->repo->getApplicationsByStatus([
            'pengajuan',
            'menunggu_acc_pengiriman',
            'pengiriman',
            'ditolak'
        ]));
    }

    public function showApplication($id)
    {
        $application = $this->service->getDetail($id);
        return response()->json($application);
    }

    public function submitApplications(Request $request)
    {
        $action = $request->input('action');
        $data = $request->all();

        if ($action === 'batal' && isset($data['id'])) {
            return response()->json(['message' => 'Pengajuan dibatalkan']);
        }

        $result = $this->service->storeApplication($data, $action);
        return response()->json($result);
    }

    public function submitPlacements(Request $request, $id)
    {
        $action = $request->input('action');
        $data = $request->all();

        $result = $this->service->placementProcess($id, $data, $action);
        return response()->json($result);
    }
}
