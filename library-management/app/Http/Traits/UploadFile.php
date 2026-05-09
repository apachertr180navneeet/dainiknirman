<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadFile
{
    /**
     * Upload file.
     */
    private function uploadFile($file, $path = null, $name = null)
    {
        $fileUploaded = false;

        // File system
        $fileSystem = config('filesystems.default');
        //------------

        // Generate file name and get extension
        $extension = $file->getClientOriginalExtension();
        $fileName  = $this->generateFileName($extension, $name);
        if(!empty($name)){
			$fileName = $name.'.'.$extension;
		}

        $fileName = str_replace(' ', '', $fileName);
		$fileName = str_replace('=', '_', $fileName);
		$fileName = str_replace('+', '_', $fileName);
		$fileName = str_replace('-', '_', $fileName);
		$fileName = str_replace('?', '_', $fileName);
		$fileName = str_replace('&', '_', $fileName);

        //-------------------------------------

        // Upload file
        try {

            // Upload file
            $file = Storage::disk($fileSystem)->putFileAs(rtrim($path, '/'), $file, $fileName);
            //------------

            if (!is_null($file)) {
                $fileUploaded = true;
            }
        } catch (\Exception $e) {
            //
        }
        //------------

        // Set data
        if ($fileUploaded) {
            $data = [
                '_status'  => true,
                '_message' => __('messages.file_uploaded'),
                '_data'    => $fileName
            ];
        } else {
            $data = [
                '_status'  => false,
                '_message' => __('messages.file_uploading_failed'),
                '_data'    => null
            ];
        }
        //---------

        return $data;
    }

    /**
     * Generate file name.
     *
     * @param  string  $extension
     * @return string
     */
    private function generateFileName($extension, $name)
    {
        if(!is_null($name)) {
            $name =str_replace('.', '-', pathinfo($name,PATHINFO_FILENAME));
            $nospacename =str_replace(' ', '_', $name);
            return  $nospacename. '-' . time() . '.' . $extension;
        } else {
            return Str::uuid().'-'.time().'.'.$extension;
        }
    }
}
