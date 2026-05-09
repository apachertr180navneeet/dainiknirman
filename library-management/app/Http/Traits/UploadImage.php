<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

trait UploadImage
{
    /**
	 * Upload image.
	 */
    private function uploadImage($file, $path = null, $compression = null, $name = null, $thumbSizeWidth = null, $thumbSizeHeight = null)
    {
		$image_uploaded = false;

    	// File system
		$file_system = config('filesystems.default');
		//------------

		// Generate image name and get extension
		$extension        = $file->getClientOriginalExtension();
		$file_name        = $this->generateImageName($extension, $name);
		if(!empty($name)){
			$file_name = $name.'.'.$extension;
		}

		$file_name = str_replace(' ', '_', $file_name);
		$file_name = str_replace('=', '_', $file_name);
		$file_name = str_replace('+', '_', $file_name);
		$file_name = str_replace('-', '_', $file_name);
		$file_name = str_replace('?', '_', $file_name);
		$file_name = str_replace('&', '_', $file_name);

		$destination_path = $path.$file_name;
		//--------------------------------------

		// Upload image
		try {
			if (! is_null($compression)) {

                if( $extension != 'svg'){
                    // Compress image
                    $image = Image::make($file)->encode($extension, $compression);
                    //---------------

					if(!empty($thumbSizeWidth) && !empty($thumbSizeHeight)){
						$imageThumb = $this->createThumb($file, $thumbSizeWidth, $thumbSizeHeight, $path.'thumb/', $file_name);
					}
					else{
						// Upload image
						$image_uploaded = Storage::disk($file_system)->put($destination_path, $image->stream());
						//-------------
					}
                } else {

                    // Upload image
                    $image = Storage::disk($file_system)->putFileAs(rtrim($path, '/'), $file, $file_name);
                    //-------------
                }

                if (! is_null($image)) {
					$image_uploaded = true;
				}

			} else {

				if($extension != 'svg'){

					if(!empty($thumbSizeWidth) && !empty($thumbSizeHeight)){
						$imageThumb = $this->createThumb($file, $thumbSizeWidth, $thumbSizeHeight, $path.'thumb/', $file_name);
					}
					else{
						// Upload image
						$image = Storage::disk($file_system)->putFileAs(rtrim($path, '/'), $file, $file_name);
						//-------------
					}

					if (! is_null($image)) {
						$image_uploaded = true;
					}
				}
				else{
					// Upload image
                    $image_uploaded = Storage::disk($file_system)->putFileAs(rtrim($path, '/'), $file, $file_name);
                    //-------------
				}
			}
		} catch (\Exception $e) {
			// p($e->getMessage());
		}
		//-------------

		// Set data
		if ($image_uploaded) {
			$data = [
				'_status'  => true,
				'_message' => __('messages.image_uploaded'),
				'_data'    => $file_name
			];
		} else {
			$data = [
				'_status'  => false,
				'_message' => __('messages.image_uploading_failed'),
				'_data'    => null
			];
		}
		//---------

		return $data;
    }

	/**
	 * Upload image new method.
	 * @param additionalConfig ['thumb_width', 'thumb_height', 'use_thumb_folder']
	 */
    private function uploadImageByConfig($file, $path = null, $compression = null, $name = null, $additionalConfig = [])
    {
		$image_uploaded = false;

    	// File system
		$file_system = config('filesystems.default');
		//------------

		// Generate image name and get extension
		$extension        = $file->getClientOriginalExtension();
		$file_name        = $this->generateImageName($extension, $name);

		if(!empty($name)){
			$file_name = $name.'.'.$extension;
		}

		$file_name = str_replace(' ', '_', $file_name);
		$file_name = str_replace('=', '_', $file_name);
		$file_name = str_replace('+', '_', $file_name);
		$file_name = str_replace('-', '_', $file_name);
		$file_name = str_replace('?', '_', $file_name);
		$file_name = str_replace('&', '_', $file_name);
		
		$destination_path = $path.$file_name;
		//--------------------------------------

		// Upload image
		try {
			if(!is_null($compression))
			{
                if($extension != 'svg'){
                    // Compress image
                    $image = Image::make($file)->encode($extension, $compression);
                    //---------------

					if(!empty($additionalConfig['thumb_width']) && !empty($additionalConfig['thumb_height'])){
						$thumbSizeWidth = $additionalConfig['thumb_width'];
						$thumbSizeHeight = $additionalConfig['thumb_height'];

						if($additionalConfig['use_thumb_folder'] == true){
							$imageThumb = $this->createThumb($file, $thumbSizeWidth, $thumbSizeHeight, $path.'thumb/', $file_name);
						}
						else{
							$image = Image::make($file);
							$image = $image->resize($thumbSizeWidth, $thumbSizeHeight, function($constraint){
								$constraint->aspectRatio();
							});

							$imageThumb = Storage::disk($file_system)->put($destination_path, $image->stream(), 0777);
						}
					}
					else{
						// Upload image
						$image_uploaded = Storage::disk($file_system)->put($destination_path, $image->stream(), 0777);
						//-------------
					}
                } else {

                    // Upload image
                    $image = Storage::disk($file_system)->putFileAs(rtrim($path, '/'), $file, $file_name);
                    //-------------
                }

                if (! is_null($image)) {
					$image_uploaded = true;
				}

			} else {

				if($extension != 'svg'){

					if(!empty($additionalConfig['thumb_width']) && !empty($additionalConfig['thumb_height'])){
						$thumbSizeWidth = $additionalConfig['thumb_width'];
						$thumbSizeHeight = $additionalConfig['thumb_height'];

						if($additionalConfig['use_thumb_folder'] == true){
							$imageThumb = $this->createThumb($file, $thumbSizeWidth, $thumbSizeHeight, $path.'thumb/', $file_name);
						}
						else{
							$image = Image::make($file);
							$image = $image->resize($thumbSizeWidth, $thumbSizeHeight, function($constraint){
								$constraint->aspectRatio();
							});

							$imageThumb = Storage::disk($file_system)->put($destination_path, $image->stream(), 0777);
						}
					}
					else{
						// Upload image
						$image = Storage::disk($file_system)->putFileAs(rtrim($path, '/'), $file, $file_name, 0777);
						//-------------
					}

					if (! is_null($image)) {
						$image_uploaded = true;
					}
				}
				else{
					// Upload image
                    $image_uploaded = Storage::disk($file_system)->putFileAs(rtrim($path, '/'), $file, $file_name);
                    //-------------
				}
			}
		} catch (\Exception $e) {
			// Exception
		}
		//-------------

		// Set data
		if ($image_uploaded) {
			$data = [
				'_status'  => true,
				'_message' => __('messages.image_uploaded'),
				'_data'    => $file_name
			];
		} else {
			$data = [
				'_status'  => false,
				'_message' => __('messages.image_uploading_failed'),
				'_data'    => null
			];
		}
		//---------

		return $data;
    }

	/**
	 * Generate image name.
	 *
	 * @param  string  $extension
	 * @return string
	 */
	private function generateImageName($extension, $name)
	{
        if(!is_null($name)) {
			$name = str_replace(' ', '_', $name);
            return  $name . '-' . time() . '.' . $extension;
        } else {
            return Str::uuid().'-'.time().'.'.$extension;
        }
	}

	public function createThumb($image, $thumbSizeWidth = 100, $thumbSizeHeight = 100, $thumbPath, $imageName)
	{
		// File system
		$file_system = config('filesystems.default');
		//------------

		// Create thumb directory if not exists
		if(!Storage::disk($file_system)->exists($thumbPath))
		{
			Storage::makeDirectory($thumbPath);
		}

		$image = Image::make($image);
		$image = $image->resize($thumbSizeWidth, $thumbSizeHeight, function($constraint){
			$constraint->aspectRatio();
		});

		$image = Storage::disk($file_system)->put($thumbPath.$imageName, $image->encode(), 0777);

		return $image;
	}

	private function checkAndApplyImageNameReplacement($file_name)
	{
		if(!empty($file_name)){
			$file_name = str_replace(' ', '_', $file_name);
			$file_name = str_replace('=', '_', $file_name);
			$file_name = str_replace('+', '_', $file_name);
			$file_name = str_replace('-', '_', $file_name);
			$file_name = str_replace('?', '_', $file_name);
			$file_name = str_replace('&', '_', $file_name);
		}

		return $file_name;
	}
}
