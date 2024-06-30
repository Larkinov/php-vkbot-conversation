<?php

namespace vkbot\classes;

use vkbot\config\Config;
use vkbot\config\TypeCache;
use vkbot\utils\WriterLogs;
use vkbot\classes\Profile;

class Conversation
{

    private $cache;
    private $peer_id;

    public function __construct($peer_id)
    {
        $this->peer_id = $peer_id;
        $this->cache = new Cache(TypeCache::CONVERSATION, $peer_id);
    }

    public function getCache($keyCache = "")
    {
        (new WriterLogs())->writeLog("get $this->peer_id-conversation cache", Config::LOG_CONVERSATION);
        if (!empty($keyCache)) {
            $cache = $this->cache->getCache();
            return $cache[$keyCache];
        } else
            return $this->cache->getCache();
    }

    public function hasCache($keyCache = "")
    {
        $cache = $this->cache->getCache();
        if (!empty($cache)) {
            if ($keyCache) {
                if (isset($cache[$keyCache]))
                    return true;
                else
                    return false;
            } else
                return true;
        }
        return false;
    }
    public function setCache(array $data)
    {
        if ($this->hasCache()) {
            $fullCache = $this->cache->getCache();
            foreach ($data as $key => $value) {
                $fullCache[$key] = $value;
            }
            $this->cache->saveCache($fullCache);
        } else
            $this->cache->saveCache($data);
    }

    public function deleteCache()
    {
        $this->cache->saveCache([]);
    }

    public function hasAccess()
    {
        try {
            $request_params = [
                'access_token' => Config::TOKEN,
                'v' => Config::VERSION,
                'peer_id' => $this->peer_id,
            ];

            $request_url = 'https://api.vk.com/method/messages.getConversationMembers?' . http_build_query($request_params);
            $response = file_get_contents($request_url);


            if ($response !== false) {
                $response_data = json_decode($response, true);
                if (isset($response_data['error']['error_code'])) {
                    if ($response_data['error']['error_code'] === "917") {
                        (new WriterLogs)->writeLog("error: dont have access, chat - $this->peer_id", Config::LOG_CONVERSATION);
                        return false;
                    } else {
                        (new WriterLogs)->writeLog("error: check this code error - $response_data[error][error_code], chat - $this->peer_id", Config::LOG_CONVERSATION);
                        return false;
                    }
                } else {
                    return true;
                }
            } else {
                throw new \Exception("error has access - $response");
            }
            return false;
        } catch (\Throwable $th) {
            return (new WriterLogs())->writeErrorLog($th, "Bot.php -> hasAccess()");
        }
    }

    public function getInfoPeople()
    {
        try {
            $request_params = [
                'access_token' => Config::TOKEN,
                'v' => Config::VERSION,
                'peer_id' => $this->peer_id,
            ];

            $request_url = 'https://api.vk.com/method/messages.getConversationMembers?' . http_build_query($request_params);
            $response = file_get_contents($request_url);

            if ($response !== false) {
                $response_data = json_decode($response, true);
                if (isset($response_data['response'])) {
                    $profiles = [];
                    foreach ($response_data['response']['profiles'] as $value) {
                        $profile = new Profile($value['id'], $value['sex'], $value['first_name'], $value['last_name'], 0, 0, 0, 0);
                        array_push($profiles, $profile);
                    }
                    $infoPeople = [
                        "count" => $response_data['response']['count'],
                        "profiles" => $profiles,
                    ];

                    return $infoPeople;
                } else {
                    throw new \Exception("$response_data[error][error_msg]");
                }
            } else {
                throw new \Exception("error send message - $response");
            }
            return false;
        } catch (\Throwable $th) {
            return (new WriterLogs())->writeErrorLog($th, "Bot.php -> sendMessage()");
        }
    }


    public function updateAdminInfo()
    {
        try {
            $request_params = [
                'access_token' => Config::TOKEN,
                'v' => Config::VERSION,
                'peer_id' => $this->peer_id,
            ];

            $request_url = 'https://api.vk.com/method/messages.getConversationMembers?' . http_build_query($request_params);
            $response = file_get_contents($request_url);

            if ($response !== false) {
                $response_data = json_decode($response, true);
                if (isset($response_data['response'])) {
                    $infoPeople = $this->getCache();
                    foreach ($infoPeople['profiles'] as $key => $people) {
                        foreach ($response_data['response']['items'] as $profile) {
                            if ($people['id'] === $profile['member_id']) {
                                $infoPeople['profiles'][$key]['isAdmin'] = isset($profile['is_admin']) ? "1" : "0";
                            }
                        }
                    }
                    $this->setCache(array("profiles" => $infoPeople['profiles']));
                } else {
                    throw new \Exception("$response_data[error][error_msg]");
                }
            } else {
                throw new \Exception("error send message - $response");
            }
            return false;
        } catch (\Throwable $th) {
            return (new WriterLogs())->writeErrorLog($th, "Bot.php -> sendMessage()");
        }
    }
}
