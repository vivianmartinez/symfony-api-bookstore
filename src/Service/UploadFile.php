<?php

namespace App\Service;

use League\Flysystem\FilesystemOperator;

class UploadFile{

    private FilesystemOperator $storage;
    
    // The variable name $defaultStorage matters: it needs to be the camelized version
    // of the name of your storage. 
    public function __construct(FilesystemOperator $bookCoverStorage)
    {
        $this->storage = $bookCoverStorage;
    }

    public function uploadImageBase64(string $base64File):string
    {
        //get image extension
        $extension = explode('/',mime_content_type($base64File))[1];
        $path = sprintf('%s.%s',uniqid('book_',true),$extension);
        //get content image base 64 and decode base64
        $content = base64_decode(explode(',',$base64File)[1]); 
        $this->storage->write($path,$content);
        return $path;
    }

}