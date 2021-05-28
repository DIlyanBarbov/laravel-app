<?php


namespace App\Providers;


use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;

class GcsServiceProvider
{
    /**
     * @return Bucket
     */
    public static function getBucket()
    {
        $googleConfigFile  = file_get_contents(config_path('googlecloud.json'));
        $storage           = new StorageClient([
            'keyFile' => json_decode($googleConfigFile, true),
        ]);
        $storageBucketName = config('googlecloud.storage_bucket');
        return $storage->bucket($storageBucketName);
    }
}
