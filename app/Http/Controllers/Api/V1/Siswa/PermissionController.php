<?php

namespace App\Http\Controllers\Api\V1\Siswa;

use App\Services\Siswa\PermissionService;
use Illuminate\Http\Request;

class PermissionController
{
    protected $service;
    public function __construct(PermissionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->roles()->where('name', 'pembimbing')->exists()) {
            $data = $this->service->getPermissionsBySupervisor($user->id);
        } else {
            $data = $this->service->getPermissionsByStudent($user->id);
        }

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $internship = $request->user()->student->internship;
        $request->validate([
            'start_date' => [
                'required',
                'date',
                'after_or_equal:' . $internship->start_date,
                'before_or_equal:' . $internship->end_date,
            ],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
                'before_or_equal:' . $internship->end_date,
            ],
            'type' => 'required|in:sick,leave',
            'reason' => 'required|string',
            'attachment' => 'required|image|max:2048'
        ]);

        $internshipId = $request->user()->student->internship->id;
        $this->service->submitPermission($internshipId, $request->all(), $request->file('attachment'));

        return response()->json(['message' => 'Izin berhasil diajukan, silakan menunggu persetujuan dari guru pembimbing.']);
    }

    public function verify(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);
        $this->service->handleVerification($id, $request->status);
        return response()->json(['message' => 'Status izin berhasil diperbarui.']);
    }
}
