<?php

namespace vkbot\classes;

use vkbot\config\Config;
use vkbot\config\TypeCache;
use vkbot\utils\FileWorker;
use vkbot\utils\WriterLogs;

class Cache
{

    public $typeCache = "";
    public $data = [];
    private $id_cache = "";

    public function __construct(string $typeCache, $id_cache="")
    {
        $this->typeCache = $typeCache;
        $this->id_cache = $id_cache;
    }

    private function getPathCache()
    {
        try {
            $pathCache = "";
            switch ($this->typeCache) {
                case (TypeCache::SERVER):
                    $pathCache =  __DIR__ . "/../../cache/" . TypeCache::SERVER;
                    break;
                case (TypeCache::LAST_TS):
                    $pathCache =  __DIR__ . "/../../cache/" . TypeCache::LAST_TS;
                    break;
                case (TypeCache::CONVERSATION):
                    $pathCache =  __DIR__ . "/../../cache/" . TypeCache::CONVERSATION . $this->id_cache;
                    break;
                default:
                    throw new \Error("error type cache");
            }
            return $pathCache;
        } catch (\Throwable $th) {
            return (new WriterLogs)->writeErrorLog($th, "Cache.php -> getPathCache()");
        }
    }

    private function checkCacheData()
    {
        try {
            $path = $this->getPathCache($this->typeCache);

            if ($path) {
                $fw = new FileWorker();
                $cache = $fw->outputData($path, true, "cache");
                if ($cache === "not found cache") {
                    (new WriterLogs())->writeLog("get cache: " . $this->typeCache . " - not found",Config::LOG_CONVERSATION);
                    return 0;
                }
                $this->data = $cache;
                return 1;
            } else
                return 0;
        } catch (\Throwable $th) {
            return (new WriterLogs())->writeErrorLog($th, "Cache.php -> getCache.php()");
        }
    }



    public function saveCache(array $cacheData)
    {
        $fw = new FileWorker();
        $fw->writeData($cacheData, $this->getPathCache(), false);
    }

    public function getCache()
    {
        if ($this->checkCacheData() && count($this->data) > 0) {
            return $this->data;
        } else
            return 0;
    }

    public function deleteCache()
    {
        $fw = new FileWorker();
        $fw->writeData([], $this->getPathCache(), false);
    }
}
