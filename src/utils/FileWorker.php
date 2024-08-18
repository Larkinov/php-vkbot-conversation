<?php

namespace vkbot_conversation\utils;

class FileWorker
{

    public static function writeData(string $pathfile, object $data)
    {
        echo "<br>$pathfile - write data<br>";
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
        echo "<br>$pathfile - read data<br>";
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

    public static function clearFile(string $pathfile)
    {
        echo "<br>$pathfile - clear data<br>";
        $file = fopen($pathfile, "w");
        if (!$file)
            throw new \Exception("Error open file");
        file_put_contents($pathfile, "");
        fclose($file);
    }

    public static function createEmptyFile(string $pathfile): bool
    {
        echo "<br><pre>$pathfile</pre> - pathfile createEmptyFile<br>";
        $directory = dirname($pathfile);
        if (!is_dir($directory)) {
            mkdir($directory, 0770, true);
        }
        if (file_put_contents($pathfile, '') !== false) {
            return true;
        } else {
            return false;
        }
    }
}
