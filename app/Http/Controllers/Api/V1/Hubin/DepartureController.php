<?php

namespace App\Http\Controllers\Api\V1\Hubin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Internship;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Letter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DepartureController extends Controller
{
    public function index()
    {
        $internships = Internship::with(['student.user', 'student.major', 'industry', 'pembimbing', 'letters'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($intern) {
                $statusText = 'Menunggu';
                if ($intern->status === 'active') $statusText = 'Disetujui';
                if ($intern->status === 'cancelled') $statusText = 'Dibatalkan';

                return [
                    'id' => $intern->id,
                    'nis' => $intern->student->nis ?? '-',
                    'studentName' => $intern->student->user->name ?? '-',
                    'major' => $intern->student->major->major_name ?? ($intern->student->jurusan ?? '-'),
                    'industry' => $intern->industry->name ?? '-',
                    'startDate' => $intern->start_date ? Carbon::parse($intern->start_date)->translatedFormat('d F Y') : '-',
                    'endDate' => $intern->end_date ? Carbon::parse($intern->end_date)->translatedFormat('d F Y') : '-',
                    'status' => $statusText,
                    'industry_id' => $intern->industry_id,
                    'pembimbing_id' => $intern->pembimbing_id,
                    'has_letter' => $intern->letters()->count() > 0,
                ];
            });

        return response()->json($internships);
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'reason' => 'required_if:action,reject|string|nullable'
        ]);

        $internship = Internship::findOrFail($id);

        if ($request->action === 'approve') {
            $internship->status = 'active';
            $internship->save();
            return response()->json(['message' => 'Keberangkatan berhasil disetujui. Surat pengantar siap dicetak.']);
        } 
        else {
            $internship->industry_id = null;
            $internship->start_date = null;
            $internship->end_date = null;
            $internship->duration_month = null;
            $internship->status = 'Pending';
            $internship->save();

            return response()->json(['message' => 'Penempatan berhasil dibatalkan. Siswa kembali ke status Belum Ditempatkan.']);
        }
    }

     public function generateSurat(Request $request, $id)
    {
        $internship = Internship::with(['student.user', 'industry', 'student.major'])->findOrFail($id);

        if ($internship->status !== 'active') {
            return response()->json(['message' => 'Hanya status Disetujui yang bisa dicetak!'], 400);
        }

        $existingLetter = Letter::where('internship_id', $internship->id)->first();
        if ($existingLetter) {
            return response()->json(['message' => 'Surat pengantar sudah dibuat untuk keberangkatan ini.']);
        }

         $data = [
            'tanggalSurat' => Carbon::now()->translatedFormat('d F Y'),
            'namaPerusahaan' => $internship->industry->name,
            'alamatPerusahaan' => $internship->industry->address ?? '-',
            'hrName' => $internship->industry->hr_name ?? 'HRD Manager',
            'namaSiswa' => $internship->student->user->name,
            'nisSiswa' => $internship->student->nis,
            'jurusanSiswa' => $internship->student->major->major_name ?? $internship->student->jurusan,
            'tanggalMulai' => Carbon::parse($internship->start_date)->translatedFormat('d F Y'),
            'durasi' => $internship->duration_month,
        ];

        $pdf = Pdf::loadView('pdf.surat-pengantar', $data);
        $fileName = 'Surat_Pengantar_' . Str::slug($internship->student->user->name) . '_' . time() . '.pdf';
        $filePath = 'surat_pengantar/' . $fileName;

        Storage::disk('public')->put($filePath, $pdf->output());
        $letterNumber = '421.5/' . str_pad($internship->id, 3, '0', STR_PAD_LEFT) . '/HUBIN/' . date('Y');

        Letter::create([
            'internship_id' => $internship->id,
            'letter_number' => $letterNumber,
            'status' => 'approved',
            'file_path' => $filePath,
        ]);

        return response()->json(['message' => 'Surat pengantar berhasil dibuat dan disimpan.', 'letter_number' => $letterNumber]);
        
    }

    public function viewSurat($id)
    {
        $letter = Letter::where('internship_id', $id)->first();
        if (!$letter) {
            return response()->json(['message' => 'Surat belum di-generate!'], 404);
        }

        return response()->json([
            'file_url' => asset('storage/' . $letter->file_path)
        ]);
    }
}