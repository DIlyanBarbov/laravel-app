<?php

namespace App\Http\Controllers;

use App\Models\File;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function edit(Request $request)
    {
        if ($request->method() === 'GET') {
            return view('edit', ['pictures' => $this->pictures()]);
        }

        if ($request->method() === 'POST' && $newUsername = $request->post('name')) {
            $validator = Validator::make($request->all(), [
                'name' => 'unique:users|max:255',
            ]);

            if ($validator->fails()) {
                return redirect('/edit')->withErrors($validator)->withInput();
            }
            Log::info('name');
        }
        if ($request->method() === 'POST' && $newEmail = $request->post('email')) {
            $validator = Validator::make($request->all(), [
                'email' => 'email',
            ]);
            if ($validator->fails()) {
                return redirect('/edit')->withErrors($validator)->withInput();
            }
            Log::info('email');
        }

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
//                'predefinedAcl' => 'publicRead',
                'name' => $gcsPath,
            ]);

            $user_id = Auth::user()->getAuthIdentifier();
            if ($so) {
                $file->fill([
                    'file_name' => $fileName,
                    'path'      => 'uploads/',
                    'mime_type' => $mime,
                    'user_id'   => $user_id,
                ]);
                $file->save();
                return view('edit', ['pictures' => $this->pictures()]);
            }

        }
        return view('edit', ['pictures' => $this->pictures()]);
    }

    public function pictures()
    {

        $user_id = Auth::user()->getAuthIdentifier();

        $files = File::all()->where('user_id', '=', $user_id);
        $urls  = [];
        foreach ($files as $file) {
//            $bucket  = $this->getBucket();
//            $img  = $bucket->object('uploads/' . $file['file_name'] . $file['mime_type']);
//            $url = $img->signedUrl(new \DateTime('15 minutes'), [
//                'version' => 'v4',
//            ]);
            $gcs    = Storage::disk('gcs');
            $url    = $gcs->url('uploads/' . $file['file_name'] . $file['mime_type']);
            $newUrl = substr_replace($url, '%20', 71, 0);
            $urls[] = $newUrl;
        }


        return $urls;
    }
}
