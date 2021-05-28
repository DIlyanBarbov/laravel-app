<?php


namespace App\BL;


use App\Models\File;
use App\Providers\GcsServiceProvider;
use Google\Cloud\Storage\StorageObject;

class FileBL extends File
{
    /**
     * @var string $ext
     */
    private $ext;

    /**
     * @param $mimeType
     */
    public function setExt($mimeType)
    {
        $this->ext = str_contains($mimeType, 'png') ? '.png' : '.jpg';
    }

    /**
     * @return mixed
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * @return bool|null
     */
    public function delete()
    {
        if (parent::delete()) {
            $bucket  = GcsServiceProvider::getBucket();
            $gcsPath = 'uploads/' . $this['file_name'] . $this['mime_type'];
            $img     = $bucket->object($gcsPath);
            $img->delete();
            return true;
        }
        return false;
    }

    /**
     * @param $fileName
     * @param $fileToUpload
     *
     * @return StorageObject
     */
    public function upload($fileName, $fileToUpload)
    {
        $bucket     = GcsServiceProvider::getBucket();
        $filepath   = $fileToUpload->storeAs('uploads', $fileName . $this->getExt(), 'public');
        $gcsPath    = 'uploads/' . $fileName . $this->getExt();
        $publicPath = storage_path('app/public/uploads/' . $fileName . $this->getExt());

        $fileSource = fopen($publicPath, 'rb');
        return $bucket->upload($fileSource, [
            'predefinedAcl' => 'publicRead',
            'name'          => $gcsPath,
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getUrl()
    {
        $bucket  = GcsServiceProvider::getBucket();
        $gcsPath = 'uploads/' . $this['file_name'] . $this['mime_type'];
        $img     = $bucket->object($gcsPath);
        return $img->signedUrl(new \DateTime('15 minutes'), [
            'version' => 'v4',
        ]);
    }

}
