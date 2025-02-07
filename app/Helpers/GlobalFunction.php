<?php

namespace App\Helpers;
class GlobalFunction
{
    public static function handleImageUpload($image, $name, $path = '')
    {
        if ($image == null) {
            return null;
        }
        $extension = $image->getClientOriginalExtension();
        $filename = $name . '.' . $extension;
        $path = public_path('dist/assets/img/' . $path);
        $image->move($path, $filename);
        return $filename;
    }
}
