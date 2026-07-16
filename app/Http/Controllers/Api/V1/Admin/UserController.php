<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;
use App\Imports\UsersImport;
use App\Jobs\ProcessUserActivation;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\AuthMail;

class UserController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $import = new UsersImport;
            Excel::import($import, $request->file('file'));

            $failures = $import->failures();

            if ($failures->isNotEmpty()) {
                $failedRows = [];
                foreach ($failures as $failure) {
                    $rowNumber = $failure->row();
                    $errors = implode('; ', $failure->errors());

                    if (!isset($failedRows[$rowNumber])) {
                        $failedRows[$rowNumber] = array_merge($failure->values(), [
                            'alasan_gagal' => $errors
                        ]);
                    } else {
                        $failedRows[$rowNumber]['alasan_gagal'] .= '; ' . $errors;
                    }
                }

                $rowsToExport = [];
                foreach ($failedRows as $row) {
                    $rowsToExport[] = [
                        'nama' => $row['nama'] ?? '',
                        'email' => $row['email'] ?? '',
                        'role' => $row['role'] ?? '',
                        'identifier' => $row['identifier'] ?? '',
                        'phone' => $row['phone'] ?? '',
                        'address' => $row['address'] ?? '',
                        'jurusan' => $row['jurusan'] ?? '',
                        'kelas' => $row['kelas'] ?? '',
                        'tahun_ajaran' => $row['tahun_ajaran'] ?? '',
                        'alasan_gagal' => $row['alasan_gagal'] ?? '',
                    ];
                }

                $headings = ['nama', 'email', 'role', 'identifier', 'phone', 'address', 'jurusan', 'kelas', 'tahun_ajaran', 'Alasan Gagal'];
                $fileName = 'temp/error_report_' . Str::random(10) . '.xlsx';

                Excel::store(new \App\Exports\ImportErrorReportExport($rowsToExport, $headings), $fileName, 'public');
                $errorReportUrl = asset('storage/' . $fileName);

                $failedRowsCount = count($failedRows);
                $successfulRowsCount = $import->successCount;
                $totalRowsProcessed = $successfulRowsCount + $failedRowsCount;

                AuditLog::record(
                    'm_users',
                    'import',
                    "Mengimpor data pengguna dari file Excel. Berhasil: {$successfulRowsCount}, Gagal: {$failedRowsCount} (Laporan error dibuat)"
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Proses import selesai dengan beberapa catatan.',
                    'summary' => [
                        'total_rows_processed' => $totalRowsProcessed,
                        'successful_rows' => $successfulRowsCount,
                        'failed_rows' => $failedRowsCount
                    ],
                    'error_report_url' => $errorReportUrl
                ]);
            }

            $successfulRowsCount = $import->successCount;

            AuditLog::record(
                'm_users',
                'import',
                "Mengimpor data pengguna dari file Excel. Berhasil: {$successfulRowsCount}, Gagal: 0"
            );

            return response()->json([
                'success' => true,
                'message' => 'Proses import selesai 100% dengan sukses.',
                'summary' => [
                    'total_rows_processed' => $successfulRowsCount,
                    'successful_rows' => $successfulRowsCount,
                    'failed_rows' => 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengimpor data: ' . $e->getMessage()], 500);
        }
    }

    public function resendActivationEmail($id)
    {
        $user = User::findOrFail($id);

        ProcessUserActivation::dispatch($user);

        return response()->json(['message' => 'Email aktivasi berhasil dikirim ulang ke ' . $user->email]);
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        $token = Str::random(60);
        DB::table('tr_password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $link = env('FRONTEND_URL', 'http://localhost:5173') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
        Mail::to($user->email)->send(new AuthMail($user, $link, 'reset'));

        AuditLog::record(
            'm_users',
            'reset-password',
            "Mengirimkan link reset password untuk pengguna {$user->name} ({$user->email})"
        );

        return response()->json(['message' => 'Link reset password berhasil dikirim ke email pengguna.']);
    }
}
