<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Vish4395\LaravelFileViewer\LaravelFileViewer;

class FilePreviewController extends Controller
{
    /**
     * Handle the incoming request.
     */
     public function __invoke(string $encrypted)
    {
        try {
            // Decode + decrypt
            $filePath = Crypt::decryptString(base64_decode($encrypted));
        } catch (\Exception $e) {
            abort(403, 'Invalid or expired file link.');
        }

        $disk = 'public';
        $fileUrl = asset('storage/' . $filePath);
        if (!Storage::disk($disk)->exists($filePath)) {
            abort(404, 'File not found.');
        }

        $fileName = basename($filePath);
        $fileData = [['label' => __('File Name'), 'value' => $fileName]];

        return LaravelFileViewer::show($fileName, $filePath, $fileUrl, $disk, $fileData);
    }
}