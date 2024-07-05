<?php

namespace vkbot_conversation\utils;

class FileWorker
{

    public static function writeData(string $pathfile, object $data)
    {
        $file = fopen($pathfile, "a");
        if (!$file)
            throw new \Exception("Error open file");
        if ($data !== null) {
            file_put_contents($pathfile, json_encode($data));
        }
        fclose($file);
    }

    public static function readData(string $pathfile)
    {
        $file = fopen($pathfile, "r");
        if (!$file) {
            return null;
        }
        if (file_exists($pathfile)) {
            $jsonString = json_decode(file_get_contents($pathfile));
            if (!empty($jsonString)) {
                if ($jsonString === null && json_last_error() !== JSON_ERROR_NONE)
                    throw new \Exception('error: decode json file');
                else 
                    return $jsonString;
            } else
                return null;
        } else
            throw new \Exception('error: file does not exist');
    }
}
