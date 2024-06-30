<?php

namespace vkbot\classes;

use vkbot\config\TypeCache;
use vkbot\utils\WriterLogs;
use vkbot\config\Config;

class ServerVK
{

    private $token;
    private $version;
    private $group_id;
    private $cacheObj = [];

    private $key="";
    private $urlServer="";
    private $ts="";

    public function __construct(string $token, string $version, string $group_id)
    {
        $this->token = $token;
        $this->version = $version;
        $this->group_id = $group_id;
        $this->cacheObj = new Cache(TypeCache::SERVER);
    }

    function connection()
    {
        try {
            (new WriterLogs())->writeLog("new connection", Config::LOG_SERVER);
            if ($this->checkCache()) {
                return 1;
            } else {
                $this->getServer();
            }
        }
    }

    function checkCache()
    {
        try {
            $cacheData = $this->cacheObj->getCache();
            (new WriterLogs())->writeLog("get server cache", Config::LOG_SERVER);
            if ($cacheData) {
                $this->key = $cacheData['key'];
                $this->urlServer = $cacheData['server'];
                $this->ts = $cacheData['ts'];
                return 1;
            } else
                return 0;
        } catch (\Throwable $th) {
            return (new WriterLogs)->writeErrorLog($th, "ServerVK.php -> checkCache()");
        }
    }

    function getServer(){
        try {
            $request_params = [
                'access_token' => $this->token,
                'group_id' => $this->group_id,
                'v' => $this->version
            ];
    
            $request_url = 'https://api.vk.com/method/groups.getLongPollServer?' . http_build_query($request_params);
            $response = file_get_contents($request_url);
    
            if ($response !== false) {
                $response_data = json_decode($response, true);
                if (isset($response_data['response'])) {
                    if (isset($response_data['response']['key']) && isset($response_data['response']['server']) && isset($response_data['response']['ts'])) {
                        $logData = [
                            "date" => date('Y-m-d H:i:s'),
                            "operation" => "get new server",
                            "description" => "",
                        ];
                        print_r($logData);
                        (new WriterLogs())->writeLog($logData, Config::LOG_SERVER);
                        $this->cacheObj->saveCache($response_data['response']);
                        $this->key = $response_data['key'];
                        $this->urlServer = $response_data['server'];
                        $this->ts = $response_data['ts'];
                        return 1;
                    } else
                        throw new \Exception("error send message: key, server, ts not found");
                } else
                    throw new \Exception("error send message");
            } else
                throw new \Exception("error send request");
            return 0;
        } catch (\Throwable $th) {
            return (new WriterLogs())->writeErrorLog($th, "ServerVK.php -> getServer()");
        }
        
    }

    function getKey(){
        return $this->key;
    }
    function getUrlServer(){
        return $this->urlServer;
    }
    function getTs(){
        return $this->ts;
    }
    function setTs(string $ts){
        $this->ts = $ts;
    }

}
