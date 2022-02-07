<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;



class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function saveImage( $image,$path = 's3')
    {
       if(!$image)
       {
           return null;
       }
       $filename=time().'.png';
    //    Storage::disk($path)->put($filename, base64_decode($image));

        Storage::disk($path)->put($filename,fopen($image,'r+'),'public');


        $url = Storage::disk($path)->url($filename);
       return $url;
    }
}
