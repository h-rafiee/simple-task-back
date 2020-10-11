<?php

if (! function_exists('rest')) {
    function rest($status = true, $data = [], \Exception $e = null)
    {
        $statusCode = 200;
        $message = '';
        if (!empty($e)) {
            $message = $e->getMessage();
            $statusCode = (!empty($e->getCode())) ? $e->getCode() : 500;
        }
        return response()->json([
            'status' => $status,
            'data' => $data,
            'message' => $message
        ], $statusCode, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}

if (! function_exists('uploadBase64')) {
    function uploadBase64($img, $folderPath = "uploads/")
    {
        $image_parts = explode(";base64,", $img);

        $image_type_aux = explode("image/", $image_parts[0]);

        $image_type = $image_type_aux[1];

        $image_base64 = base64_decode($image_parts[1]);

        if (!is_dir(public_path($folderPath))) {
            mkdir(public_path($folderPath), 0777, true);
        }
        $file = $folderPath . uniqid() . '.' . $image_type;
        file_put_contents(public_path($file), $image_base64);
        return $file;
    }
}
