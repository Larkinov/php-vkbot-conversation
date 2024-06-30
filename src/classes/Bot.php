<?php

namespace vkbot\classes;

use Exception;
use vkbot\config\Config as Config;
use vkbot\classes\Command;
use vkbot\config\TypeCache;
use vkbot\utils\WriterLogs;

class Bot
{
    private $eventObj;
    private $command;

    public function __construct($eventObj)
    {
        $this->eventObj = $eventObj;
        $this->command = new Command();
    }

    public function sendMessage($text)
    {
        try {
            $request_params = [
                'access_token' => Config::TOKEN,
                'v' => Config::VERSION,
                'message' => $text,
                'random_id' => '0',
                'peer_id' => $this->eventObj['peer_id'],
            ];

            $request_url = 'https://api.vk.com/method/messages.send?' . http_build_query($request_params);
            $response = file_get_contents($request_url);
            if ($response !== false) {
                $response_data = json_decode($response, true);
                if (isset($response_data['response'])) {
                    if (isset($response_data['response']['key']) && isset($response_data['response']['server']) && isset($response_data['response']['ts'])) {
                        (new WriterLogs)->writeLog("send message - $text",Config::LOG_CONVERSATION);
                        return $response_data['response'];
                    }
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

    public function startCommand(string $command)
    {
        (new WriterLogs())->writeLog("start command - $command",Config::LOG_CONVERSATION);
        $this->command->runCommand($command, $this, $this->eventObj);
    }

    public function checkCommand()
    {
        return $this->command->isCommand($this->eventObj['text']);
    }

    public function getCommand()
    {
        return $this->command->getCommandInMessage($this->eventObj['text']);
    }

    public function timeLimit()
    {
        $cacheConversation = new Conversation($this->eventObj['peer_id']);
        if ($cacheConversation->hasCache("botActive")) {

            $prev_datetime = new \DateTime($cacheConversation->getCache("botActive"));
            $current_datetime = new \DateTime();

            $interval = $prev_datetime->diff($current_datetime);

            $days_in_hours = $interval->d * 24; // Переводим дни в часы
            $hours_diff = $interval->h + $days_in_hours; // Добавляем часы из дней к общему количеству часов

            if ($hours_diff >= 8) {
                echo "Прошло больше одного дня с момента предыдущей записи.";
                return "success";
            } else {
                echo "Не прошло еще и суток с момента предыдущей записи.";
                return 8 - intval($hours_diff);
            }
        }
        return "success";
    }

    public function startEvent(string $eventConversation)
    {
        (new WriterLogs())->writeLog("start event conversation - $eventConversation",Config::LOG_CONVERSATION);

        switch ($eventConversation) {
            case Config::EVENT_CONVERSATION['inviteNewUser']:
                $this->runEventInviteNewUser();
                break;
            case Config::EVENT_CONVERSATION['kickUser']:
                $this->runEventKickUser();
                break;
        }
    }


    private function runEventInviteNewUser()
    {
        $conversation = new Conversation($this->eventObj['peer_id']);
        if ($conversation->hasCache()) {
            $cache = $conversation->getCache();
            $idPeopleInConversation = [];

            foreach ($cache['profiles'] as $value) {
                array_push($idPeopleInConversation, $value['id']);
            }

            $newUsers = $this->getUser($this->eventObj['action']['member_id']);
            foreach ($newUsers as $value) {
                if (!in_array($value['id'], $idPeopleInConversation)) {
                    array_push($cache['profiles'], ['firstName' => $value['first_name'], 'lastName' => $value['last_name'], 'sex' => $value['sex'], 'randomSelect' => 0]);
                }
            }
            $conversation->setCache($cache);
        } else {
            $infoPeople = $conversation->getInfoPeople();
            $cache = [
                'id' => $this->eventObj['peer_id'],
                'count' => intval($infoPeople['count']),
                'profiles' => $infoPeople['profiles'],
            ];
            $conversation->setCache($cache);
        }
    }
    private function runEventKickUser()
    {
        $this->sendMessage("пока Санюля");
    }

    private function getUser($id)
    {
        try {
            $request_params = [
                'access_token' => Config::TOKEN,
                'v' => Config::VERSION,
                'user_ids' => $id,
                'fields' => "sex",
            ];

            $request_url = 'https://api.vk.com/method/users.get?' . http_build_query($request_params);
            $response = file_get_contents($request_url);
            if ($response !== false) {
                $response_data = json_decode($response, true);

                if (isset($response_data['response'])) {
                    (new WriterLogs)->writeLog("get news user",Config::LOG_CONVERSATION);
                    return $response_data['response'];
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
