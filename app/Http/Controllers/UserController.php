<?php


namespace App\Http\Controllers;


use Google\Cloud\Storage\StorageClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function edit(){
        return view('edit');
    }

    public function upload(Request $request){
        $googleConfigFile = file_get_contents(config_path('googlecloud.json'));
        $storage = new StorageClient([
            'keyFile' => json_decode($googleConfigFile, true)
        ]);
        $storageBucketName = config('googlecloud.storage_bucket');
        $bucket = $storage->bucket($storageBucketName);

        $request->validate([
            'file' => 'required|mimes:jpg,png'
        ]);


    }
}
