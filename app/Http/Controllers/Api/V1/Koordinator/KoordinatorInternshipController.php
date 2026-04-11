<?php

namespace App\Http\Controllers\Api\V1\Koordinator;

use App\Http\Controllers\Controller;
use App\Repositories\Koordinator\KoordinatorInternshipRepository;
use App\Services\Koordinator\KoordinatorInternshipService;
use Illuminate\Http\Request;

class KoordinatorInternshipController extends Controller
{
    protected $service, $repo;

    public function __construct(KoordinatorInternshipService $service, KoordinatorInternshipRepository $repo)
    {
        $this->service = $service;
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $type = $request->query('type', 'pengajuan');

        $data = $this->repo->getApplicationsByType($type);

        return response()->json($data);
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

    public function extend(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:individual,batch',
            'id' => 'required|integer',
            'duration_option' => 'required|in:3_bulan,6_bulan,custom',
            'custom_end_date' => 'required_if:duration_option,custom|date'
        ]);

        $this->service->extendPlacement($validated);

        return response()->json([
            'message' => 'Perpanjangan masa PKL berhasil diproses dan dinotifikasikan.'
        ]);
    }

    public function withdraw($id)
    {
        $this->service->withdrawPlacement($id);
        return response()->json(['message' => 'Siswa berhasil ditarik dari industri.']);
    }
}
