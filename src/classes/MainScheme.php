<?php

namespace vkbot\classes;

use vkbot\config\Config;
use vkbot\utils\WriterLogs;

class MainScheme
{
    private $event = "";

    public function __construct($event)
    {
        $this->event = $event;
    }

    private function getTypeMessage($message_new)
    {
        $typeMessage = "none";
        if (isset($message_new['text']) && isset($message_new['from_id']))
            if ((strpos($message_new['text'], "determinant_bot") !== false || strpos($message_new['text'], "Определитель Бот") !== false) && $message_new['from_id'] !== "222395969") {
                (new WriterLogs())->writeLog("message for bot", Config::LOG_CONVERSATION);
                $typeMessage = "new message for bot";
            }
        if (empty($message_new['text']) && !empty($message_new['action'])) {
            if (isset($message_new['action']['type']) && ($message_new['action']['type'] === "chat_invite_user" || $message_new['action']['type'] === "chat_invite_user_by_link")) {
                $typeMessage = Config::EVENT_CONVERSATION['inviteNewUser'];
            }
            // if (isset($message_new['action']['type']) && ($message_new['action']['type'] === "chat_kick_user")) {
            //     $typeMessage = Config::EVENT_CONVERSATION['kickUser'];
            // }
        }
        return $typeMessage;
    }

    private function sortMachine($event)
    {
        try {
            $scenario = "other";
            if (isset($event['type'])) {
                switch ($event['type']) {
                    case "message_new":
                        $message_new = $event['object']['message'];
                        $scenario = $this->getTypeMessage($message_new);
                        break;
                    case "other":
                        // echo "<br><br><br>other event<br>";
                        // print_r($this->event);
                        // echo "<br><br><br><br>";
                        (new WriterLogs())->writeLog("other type event - " . $event['type'], Config::LOG_OTHER_EVENT);
                        break;
                }
            } else
                throw new \Exception("dont exist type event");
            return $scenario;
        } catch (\Throwable $th) {
            return (new WriterLogs())->writeErrorLog($th, "sort Machine");
        }
    }

    //проверка полученных событий. при разных событиях, разные действия
    function start()
    {
        $scenario = $this->sortMachine($this->event);
        if (!is_integer($scenario)) {
            switch ($scenario) {
                case "new message for bot":
                    $bot = new Bot($this->event['object']['message']);
                    if ($bot->checkCommand()) {
                        $bot->startCommand($bot->getCommand());
                    } else {
                        $bot->startCommand(Config::COMMAND_BOT['/firstInformation']);
                    }
                    break;
                case Config::EVENT_CONVERSATION['inviteNewUser']:
                    $bot = new Bot($this->event['object']['message']);
                    $bot->startEvent(Config::EVENT_CONVERSATION['inviteNewUser']);
                    break;
                case Config::EVENT_CONVERSATION['kickUser']:
                    $bot = new Bot($this->event['object']['message']);
                    $bot->startEvent(Config::EVENT_CONVERSATION['kickUser']);
                    break;
                case "other":
                    echo "<br><br><br>other event<br>";
                    print_r($this->event);
                    echo "<br><br><br><br>";
                    break;
            }
        }
    }
}
