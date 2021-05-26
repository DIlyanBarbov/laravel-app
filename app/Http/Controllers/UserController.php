<?php


namespace App\Http\Controllers;


use App\Http\Kernel;
use App\Models\File;
use App\Models\User;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function edit()
    {
        return view('edit');
    }

    private function getBucket()
    {
        $googleConfigFile  = file_get_contents(config_path('googlecloud.json'));
        $storage           = new StorageClient([
            'keyFile' => json_decode($googleConfigFile, true),
        ]);
        $storageBucketName = config('googlecloud.storage_bucket');
        return $storage->bucket($storageBucketName);
    }

    public function upload(Request $request)
    {
        $bucket = $this->getBucket();
        $request->validate([
            'file' => 'required|mimes:jpg,png',
        ]);

        $file = new File();
        if ($request->file()) {
            $fileName   = $request->file('file')->getFilename();
            $mimeType   = $request->file('file')->getMimeType();
            $mime       = str_contains($mimeType, 'png') ? '.png' : '.jpg';
            $gcsPath    = 'uploads/ ' . $fileName . $mime;
            $filepath   = $request->file('file')->storeAs('uploads', $fileName . $mime, 'public');
            $publicPath = storage_path('app/public/uploads/' . $fileName . $mime);

            $fileSource = fopen($publicPath, 'rb');
            $so         = $bucket->upload($fileSource, [
                'predefinedAcl' => 'publicRead',
                'name'          => $gcsPath,
            ]);

            $user_id = Auth::user()->getAuthIdentifier();
            if ($so) {
                $file->fill([
                    'file_name' => $fileName,
                    'path'      => $gcsPath,
                    'mime_type' => $mimeType,
                    'user_id'   => $user_id,
                ]);
                $file->save();
                return view('edit', ['success' => true]);
            }

        }
        return view('edit', ['success' => false]);
    }

    public function pictures()
    {
        $bucket  = $this->getBucket();
        $user_id = Auth::user()->getAuthIdentifier();

        $files = File::all()->where('user_id', '=', $user_id);
        return \response()->json([$files]);
    }
}
