<?php

namespace App\Http\Controllers\Api\V1\Hubin;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IndustryController extends Controller
{
    public function index(Request $request)
    {
        $query = Industry::query();
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('hr_name', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'address' => 'nullable|string',
            'hr_name' => 'required|string|max:100',
            'hr_email' => 'nullable|email|max:100',
            'hr_phone' => 'nullable|string|max:20',
            'kuota_siswa' => 'required|integer|min:1',
            'mou_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('mou_file')) {
            $validated['mou_file'] = $request->file('mou_file')->store('mou_files', 'public');
        }

        $validated['is_active'] = $request->is_active ?? true;

        $industry = Industry::create($validated);

        return response()->json(['message' => 'Data industri berhasil ditambahkan', 'data' => $industry], 201);
    }

    public function update(Request $request, $id)
    {
        $industry = Industry::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'address' => 'nullable|string',
            'hr_name' => 'required|string|max:100',
            'hr_email' => 'nullable|email|max:100',
            'hr_phone' => 'nullable|string|max:20',
            'kuota_siswa' => 'required|integer|min:1',
            'mou_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('mou_file')) {
            if ($industry->mou_file) {
                Storage::disk('public')->delete($industry->mou_file);
            }
            $validated['mou_file'] = $request->file('mou_file')->store('mou_files', 'public');
        }

        $industry->update($validated);

        return response()->json(['message' => 'Data industri berhasil diperbarui', 'data' => $industry]);
    }

    public function destroy($id)
    {
        $industry = Industry::findOrFail($id);
        
        $industry->delete();

        return response()->json(['message' => 'Data industri berhasil dihapus']);
    }
}