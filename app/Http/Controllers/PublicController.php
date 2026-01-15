<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use ZipArchive;

class PublicController extends Controller
{
    public function index()
    {
        return view('public.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $files = File::byCode($request->code)
            ->active()
            ->orderBy('uploaded_at', 'desc')
            ->get();

        if ($files->isEmpty()) {
            return back()->with('error', 'ไม่พบไฟล์ที่ต้องการ หรือไฟล์หมดอายุแล้ว');
        }

        return view('public.files', compact('files'));
    }

    public function download($id)
    {
        $file = File::findOrFail($id);

        if ($file->isExpired()) {
            abort(403, 'ไฟล์หมดอายุแล้ว');
        }

        $filePath = storage_path('app/uploads/' . $file->filename);

        if (!file_exists($filePath)) {
            abort(404, 'ไม่พบไฟล์');
        }

        return Response::download($filePath, $file->filename);
    }

    public function downloadAll($code)
    {
        $files = File::byCode($code)->active()->get();

        if ($files->isEmpty()) {
            abort(404, 'ไม่พบไฟล์ที่มีรหัส ' . $code . ' หรือไฟล์หมดอายุแล้ว');
        }

        // สร้างโฟลเดอร์ temp ถ้ายังไม่มี
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            if (!mkdir($tempDir, 0755, true)) {
                abort(500, 'ไม่สามารถสร้างโฟลเดอร์ temp ได้');
            }
        }

        $zip = new ZipArchive;
        $zipFileName = 'files_' . $code . '_' . time() . '.zip';
        $zipPath = $tempDir . DIRECTORY_SEPARATOR . $zipFileName;

        // สร้างไฟล์ ZIP
        $zipStatus = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($zipStatus !== TRUE) {
            abort(500, 'ไม่สามารถสร้างไฟล์ ZIP ได้ (Error code: ' . $zipStatus . ')');
        }

        $fileCount = 0;
        $missingFiles = [];
        foreach ($files as $file) {
            $filePath = storage_path('app/uploads/' . $file->filename);
            if (file_exists($filePath)) {
                $zip->addFile($filePath, $file->filename);
                $fileCount++;
            } else {
                $missingFiles[] = $file->filename;
            }
        }

        $zip->close();

        // ตรวจสอบว่าไฟล์ ZIP ถูกสร้างจริง
        if ($fileCount === 0) {
            abort(404, 'ไม่พบไฟล์ในระบบ: ' . implode(', ', $missingFiles));
        }

        if (!file_exists($zipPath)) {
            abort(500, 'สร้างไฟล์ ZIP ไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
        }

        return Response::download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function stream($id)
    {
        $file = File::findOrFail($id);

        if ($file->isExpired()) {
            abort(403, 'ไฟล์หมดอายุแล้ว');
        }

        $filePath = storage_path('app/uploads/' . $file->filename);

        if (!file_exists($filePath)) {
            abort(404, 'ไม่พบไฟล์');
        }

        return Response::file($filePath, [
            'Content-Type' => 'audio/wav',
            'Content-Disposition' => 'inline; filename="' . $file->filename . '"',
        ]);
    }
}
