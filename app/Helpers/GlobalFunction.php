<?php

namespace App\Helpers;
class GlobalFunction
{
    public static function handleImageUpload($image, $name, $path = '')
    {
        try {
            if ($image == null) {
                return ResponseHelper::error("image not found", 404);
            }
            $extension = $image->getClientOriginalExtension();
            $filename = $name . '.' . $extension;
            $path = public_path($path);
            $image->move($path, $filename);
            return $filename;
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    public static function genaratorNameFile($name)
    {
        return $name . '_' . uniqid();
    }

    public static function deleteSingleImage($name)
    {
        try {
            $path = public_path($name);
            if (file_exists($path)) {
                unlink($path);
            }
            return true;
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}
