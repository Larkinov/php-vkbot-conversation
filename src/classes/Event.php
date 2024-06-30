<?php

namespace vkbot\classes;

use vkbot\config\Config;
use vkbot\config\TypeCache;
use vkbot\utils\WriterLogs;

class Event
{

    private $key;
    private $urlServer;
    private $ts;
    private $failedConnection;
    private $wait = 25;
    private $cacheLastTs = [];

    public function __construct(string $key, string $server, string $ts, int $wait = 25)
    {
        $this->key = $key;
        $this->urlServer = $server;
        $this->wait = $wait;
        $this->failedConnection = "";

        $this->cacheLastTs = new Cache(TypeCache::LAST_TS);

        $lastTs = $this->cacheLastTs->getCache();
        (new WriterLogs())->writeLog("get last ts cache", Config::LOG_SERVER);
        if ($lastTs)
            $this->ts = $lastTs['ts'];
        else
            $this->ts = $ts;
    }


    function getEvent()
    {
        try {
            $request_url = "$this->urlServer?act=a_check&key={$this->key}&ts={$this->ts}&wait=$this->wait&mode=2&version=3";
            $response = file_get_contents($request_url);

            if ($response !== false) {
                $response_data = json_decode($response, true);
                if (isset($response_data['failed'])) {
                    (new WriterLogs)->writeLog("failed get event", Config::LOG_SERVER);
                    switch ($response_data['failed']) {
                        case "1":
                            (new WriterLogs)->writeLog("return new ts - $response_data[ts]",Config::LOG_SERVER,"ts time out TTL",);
                            break;
                        case "2":
                            (new WriterLogs)->writeLog("delete cache key",Config::LOG_SERVER,"key time out TTL");
                            (new Cache(TypeCache::SERVER))->deleteCache();
                            $this->failedConnection = "get new key";
                            break;
                        case "3":
                            (new WriterLogs)->writeLog("delete cache key,ts",Config::LOG_SERVER,"lost data");
                            (new Cache(TypeCache::SERVER))->deleteCache();
                            $this->failedConnection = "get new key,ts";
                            break;
                        default:
                            throw new \Exception("Error response failed: $response_data[failed] - unknown failed error");
                    }
                }
                if (isset($response_data['ts'])) {
                    (new WriterLogs())->writeLog("get event",Config::LOG_SERVER,"ts - $this->ts");
                    $this->ts = $response_data['ts'];
                    $this->cacheLastTs->saveCache(array('ts' => $response_data['ts']));
                    return $response_data;
                } else {
                    if ($this->failedConnection)
                        return $this->failedConnection;
                    else
                        throw new \Exception("Error get failed: error is not defined");
                }
            } else
                throw new \Exception("error response data");
            return false;
        } catch (\Throwable $th) {
            return (new WriterLogs())->writeErrorLog($th, "Event.php getEvent()");
        }
    }
}
