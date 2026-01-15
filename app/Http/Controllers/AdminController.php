<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = File::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('requester', 'like', "%{$search}%")
                    ->orWhere('filename', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'expired') {
                $query->expired();
            }
        }

        $files = $query->orderBy('uploaded_at', 'desc')->paginate(20);

        $stats = [
            'total' => File::count(),
            'active' => File::active()->count(),
            'expired' => File::expired()->count(),
        ];

        return view('admin.dashboard', compact('files', 'stats'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:wav,mp3,ogg,m4a|max:51200',
            'requester' => 'required|string|max:100',
            'expiry_days' => 'required|integer|min:1|max:7',
        ], [
            'files.*.mimes' => 'ไฟล์ต้องเป็นประเภท: wav, mp3, ogg, m4a เท่านั้น',
            'files.*.max' => 'ไฟล์มีขนาดใหญ่เกิน 50MB',
        ]);

        $code = $this->generateUniqueCode();
        $expiryDate = Carbon::now()->addDays((int) $request->expiry_days);
        $uploadedCount = 0;

        if (!is_dir(storage_path('app/uploads'))) {
            mkdir(storage_path('app/uploads'), 0755, true);
        }

        foreach ($request->file('files') as $index => $uploadedFile) {
            $filename = time() . '_' . $index . '_' . $this->sanitizeFilename($uploadedFile->getClientOriginalName());

            $uploadedFile->storeAs('uploads', $filename);

            File::create([
                'code' => $code,
                'filename' => $filename,
                'requester' => $request->requester,
                'expiry_date' => $expiryDate,
                'uploaded_at' => now(),
            ]);

            $uploadedCount++;
        }

        return back()->with('success', "อัปโหลดไฟล์สำเร็จ {$uploadedCount} ไฟล์ รหัสดาวน์โหลด: {$code}");
    }

    public function delete($id)
    {
        $file = File::findOrFail($id);

        $filePath = storage_path('app/uploads/' . $file->filename);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $file->delete();

        return back()->with('success', 'ลบไฟล์สำเร็จ');
    }

    public function deleteAll(Request $request)
    {
        $request->validate([
            'type' => 'required|in:all,expired',
        ]);

        $query = File::query();

        if ($request->type === 'expired') {
            $query->expired();
        }

        $files = $query->get();
        $count = $files->count();

        foreach ($files as $file) {
            $filePath = storage_path('app/uploads/' . $file->filename);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $file->delete();
        }

        $message = $request->type === 'expired'
            ? "ลบไฟล์หมดอายุสำเร็จ {$count} ไฟล์"
            : "ลบไฟล์ทั้งหมดสำเร็จ {$count} ไฟล์";

        return back()->with('success', $message);
    }

    private function generateUniqueCode()
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (File::where('code', $code)->exists());

        return $code;
    }

    private function sanitizeFilename($filename)
    {
        return preg_replace('/[^a-zA-Z0-9\._\-]/', '_', $filename);
    }
}
