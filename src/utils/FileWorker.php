<?php

namespace vkbot\utils;

use vkbot\classes\Cache;
use vkbot\utils\WriterLogs;

class FileWorker
{
    public int $idWorker;

    public function __construct()
    {
        // $this->idWorker=$idWorker;
    }

    function writeData(array $data, string $path, bool $isWritingEnd)
    {
        try {
            $modeFile = $isWritingEnd ? "a" : "w+";
            $file = fopen($path, $modeFile);
            if (!$file)
                throw new \Exception("error open file");

            if ($data !== null && json_last_error() === JSON_ERROR_NONE) {
                fwrite($file, json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL);
            }
            fclose($file);
            return 1;
        } catch (\Throwable $th) {
            return (new WriterLogs())->writeErrorLog($th, "FileWorker.php -> writerData.php()");
        }
    }

    function outputData(string $path, bool $isCache=false, $typeData = "")
    {
        try {
            $file = fopen($path, "r");
            if (!$file){
                if($isCache)
                    return "not found cache";
                throw new \Exception("error open file");
            }
            if (file_exists($path)) {
                $jsonString = file_get_contents($path);
                if (!empty($jsonString)) {
                    $dataArray = json_decode($jsonString, true);
                    if ($dataArray === null && json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('error: decode json file');
                    } else {
                        return $dataArray;
                    }
                } else {
                    return 0;
                }
            } else {
                throw new \Exception('error: file does not exist');
            }
        } catch (\Throwable $th) {
            return (new WriterLogs())->writeErrorLog($th, "FileWorker.php -> outputData.php()");
        }
    }
}
