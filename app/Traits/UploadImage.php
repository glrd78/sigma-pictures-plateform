<?php

namespace App\Traits;

use App\Models\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

trait UploadImage
{
    function uploadImage($image, $name, $content, $slug, $id = false, $width = false, $height = false, $format = false)
    {
        $img = Image::make($image);
        
        if ($width || $height) {
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        
        if ($format) {
            $filename = str_slug($name) . '.' . $format;
        } else {
            $filename = str_slug($name) . '.' . $image->getClientOriginalExtension();
        }
        
        // Check if image already exists in uploads repository and add -$i if it exists
        $path = $this->getFolderName()['upload'] . $filename;
        $pathDb = $this->getFolderName()['db'] . $filename;
        $i = 1;
        while (file_exists($path)) {
            if ($format) {
                $filename = str_slug($name) . '-' . $i . '.' . $format;
            } else {
                $filename = str_slug($name) . '-' . $i . '.' . $image->getClientOriginalExtension();
            }
            $path = $this->getFolderName()['upload'] . $filename;
            $pathDb = $this->getFolderName()['db'] . $filename;
            $i++;
        }
        $img->save($path);
        
        return $this->saveImageToDb($pathDb, $img->mime(), $content, $slug, $name, $id);
    }
    
    function getFolderName()
    {
        $currentDate = Carbon::now(); // Get current date
        $uploadsPath = '/uploads';
        $realPath = public_path() . $uploadsPath;
        // If year directory doesn't exist
        if (!is_dir($realPath . '/' . $currentDate->format('Y'))) {
            mkdir($realPath . '/' . $currentDate->format('Y'), 0755);
        }
        // If month directory doesn't exist
        if (!is_dir($realPath . '/' . $currentDate->format('Y') . '/' . $currentDate->format('m'))) {
            mkdir($realPath . '/' . $currentDate->format('Y') . '/' . $currentDate->format('m'), 0755);
        }
        
        return [
            'upload' => $realPath. '/' . $currentDate->format('Y') . '/' . $currentDate->format('m') . '/',
            'db' => $uploadsPath. '/' . $currentDate->format('Y') . '/' . $currentDate->format('m') . '/'
        ];
    }
    
    function saveImageToDb($filename, $type, $name, $content, $slug, $id = false)
    {
        $file = File::create([
            'name'       => $name,
            'content'       => $content,
            'slug'       => $slug,
            'file'       => $filename,
            'type'       => $type,
            'fk_user_id' => $id ? $id : Auth::user()->id,
        ]);
        
        return $file;
    }
}