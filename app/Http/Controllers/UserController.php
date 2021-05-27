<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function deletePicture(Request $request)
    {
        $file = File::query()->where('id', '=', $request['id'])->first();
        if ($file->delete()) {
            $bucket  = $this->getBucket();
            $gcsPath = 'uploads/' . $file['file_name'] . $file['mime_type'];
            $img     = $bucket->object($gcsPath);
            $img->delete();
            Session::flash('success', 'Successfully deleted');
            return redirect()->route('viewPictures');
        }
        Session::flash('errors', 'Failed to delete');
        return redirect()->route('viewPictures');
    }

    public function viewPictures(Request $request)
    {
        return view('viewPictures', ['pictures' => $this->pictures()]);
    }

    public function edit(Request $request)
    {
        if ($request->method() === 'GET') {
            return view('edit');
        }

        $user = User::query()->find(Auth::user()->getAuthIdentifier());
        if ($request->method() === 'POST' && $newUsername = $request->post('name')) {
            $validator = Validator::make($request->all(), [
                'name' => 'unique:users|max:255',
            ]);

            if ($validator->fails()) {
                return redirect('/edit')->withErrors($validator)->withInput();
            }

            $user->update(['name' => $newUsername]);
            $user->save();
            Session::flash('success', 'Successfully edited username.');
            return view('edit');
        }
        if ($request->method() === 'POST' && $newEmail = $request->post('email')) {
            $validator = Validator::make($request->all(), [
                'email' => 'email',
            ]);

            if ($validator->fails()) {
                return redirect('/edit')->withErrors($validator)->withInput();
            }

            $user->update(['email' => $newEmail]);
            $user->save();
            Session::flash('success', 'Successfully edited email.');
            return view('edit');
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
            $gcsPath    = 'uploads/' . $fileName . $mime;
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
                Session::flash('success', 'File Uploaded Successfully.');
                return view('edit', ['pictures' => $this->pictures()]);
            }
        }
        return view('edit');
    }

    public function pictures()
    {

        $user_id = Auth::user()->getAuthIdentifier();

        $files = File::all()->where('user_id', '=', $user_id);
        $urls  = [];
        foreach ($files as $file) {
            $bucket = $this->getBucket();
            $gcsPath = 'uploads/' . $file['file_name'] . $file['mime_type'];
            $img    = $bucket->object($gcsPath);
            $url    = $img->signedUrl(new \DateTime('15 minutes'), [
                'version' => 'v4',
            ]);
            $urls[] = [
                'id'  => $file['id'],
                'url' => $url,
            ];
        }


        return $urls;
    }
}
