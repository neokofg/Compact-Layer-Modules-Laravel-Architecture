<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
class StorageService
{
    public function putOne(UploadedFile $file, string $disk): string
    {
        $filePath = $disk . hash_hmac('sha256',$file->getClientOriginalName(), env('APP_KEY')) . '.'.$file->getClientOriginalExtension();
        Storage::disk('s3')->put($filePath, $file->getContent());
        return Storage::disk('s3')->url($filePath);
    }

    public function putMany(array $files, string $disk): array
    {
        $urls = [];
        foreach ($files as $file) {
            $filePath = $disk . hash_hmac('sha256',$file->getClientOriginalName(), env('APP_KEY')) . '.'.$file->getClientOriginalExtension();
            Storage::disk('s3')->put($filePath, $file->getContent());
            $urls[] = Storage::disk('s3')->url($filePath);
        }

        return $urls;
    }
}
