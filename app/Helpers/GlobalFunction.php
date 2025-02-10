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
            $path = public_path('uploads/' . $path);
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
            $path = public_path('uploads/' . $name);
            if (file_exists($path)) {
                unlink($path);
            }
            return true;
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    public static function deleteFolder($folderPath)
    {
        if (!is_dir($folderPath)) {
            return false;
        }

        $files = array_diff(scandir($folderPath), array('.', '..'));

        foreach ($files as $file) {
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
            is_dir($filePath) ? deleteFolder($filePath) : unlink($filePath);
        }

        return rmdir($folderPath);
    }
}
