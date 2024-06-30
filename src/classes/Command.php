<?php

namespace vkbot\classes;

use vkbot\config\Config;
use vkbot\utils\WriterLogs;

class Command
{

    public function isCommand(string $message)
    {
        $command = "";
        if (stripos($message, "/") !== false) {
            $command = substr($message, stripos($message, "/"));
            if (in_array($command, Config::COMMAND_BOT))
                return true;
            else
                return false;
        }
        return false;
    }

    public function getCommandInMessage(string $message)
    {
        $command = substr($message, stripos($message, "/"));
        return $command;
    }

    public function runCommand(string $command, Bot $bot, array $event)
    {
        switch ($command) {
            case Config::COMMAND_BOT['/старт']:
                $this->runStartBot($bot, $event);
                $this->checkNewPeriod($bot, $event);
                break;
            case Config::COMMAND_BOT['/статистика']:
                $this->runStatisticsAll($bot, new Conversation($event['peer_id']), $event);
                break;
            case Config::COMMAND_BOT['/режим']:
                break;
            case Config::COMMAND_BOT['/помощь']:
                $this->runHelpBot($bot);
                break;
            case Config::COMMAND_BOT['/clear']:
                $this->runClear($bot, $event);
                break;
            case Config::COMMAND_BOT['/firstInformation']:
                $this->runFirstInformation($bot);
                break;
        }
    }

    private function runStartBot(Bot $bot, array $event)
    {
        if ($bot->timeLimit() === "success") {
            $conversation = new Conversation($event['peer_id']);
            if ($conversation->hasAccess()) {
                if ($conversation->hasCache()) {
                    $infoPeople = $conversation->getCache();
                    $rand = array_rand($infoPeople['profiles']);
                    if ($this->checkBigFail()) {
                        foreach ($infoPeople['profiles'] as $key=>$human) {
                            $infoPeople['profiles'][$key]['randomSelectMonth'] = intval($human['randomSelectMonth']) + 1;
                            $infoPeople['profiles'][$key]['randomSelectWeek'] = intval($human['randomSelectWeek']) + 1;
                            $infoPeople['profiles'][$key]['randomSelect'] = intval($human['randomSelect']) + 1;
                        }
                        $message = TextMessage::getAllLosersMessage($infoPeople['profiles'][$rand]['firstName'], $infoPeople['profiles'][$rand]['lastName'], $infoPeople['profiles'][$rand]['sex']);
                    } else {
                        $infoPeople['profiles'][$rand]['randomSelectMonth'] = intval($infoPeople['profiles'][$rand]['randomSelectMonth']) + 1;
                        $infoPeople['profiles'][$rand]['randomSelectWeek'] = intval($infoPeople['profiles'][$rand]['randomSelectWeek']) + 1;
                        $infoPeople['profiles'][$rand]['randomSelect'] = intval($infoPeople['profiles'][$rand]['randomSelect']) + 1;
                        $message = TextMessage::getLoserMessage($infoPeople['profiles'][$rand]['firstName'], $infoPeople['profiles'][$rand]['lastName'], $infoPeople['profiles'][$rand]['sex']);
                    }
                } else {
                    $infoPeople = $conversation->getInfoPeople();
                    $rand = array_rand($infoPeople['profiles']);
                    if ($this->checkBigFail()) {
                        foreach ($infoPeople['profiles'] as $human) {
                            $human->randomSelectMonth = intval($human->randomSelectMonth) + 1;
                            $human->randomSelectWeek = intval($human->randomSelectWeek) + 1;
                            $human->randomSelect = intval($human->randomSelect) + 1;
                        }
                        $message = TextMessage::getAllLosersMessage($infoPeople['profiles'][$rand]->firstName, $infoPeople['profiles'][$rand]->lastName, $infoPeople['profiles'][$rand]->sex);
                    } else {
                        $infoPeople['profiles'][$rand]->randomSelectMonth = intval($infoPeople['profiles'][$rand]->randomSelectMonth) + 1;
                        $infoPeople['profiles'][$rand]->randomSelectWeek = intval($infoPeople['profiles'][$rand]->randomSelectWeek) + 1;
                        $infoPeople['profiles'][$rand]->randomSelect = intval($infoPeople['profiles'][$rand]->randomSelect) + 1;
                        $message = TextMessage::getLoserMessage($infoPeople['profiles'][$rand]->firstName, $infoPeople['profiles'][$rand]->lastName, $infoPeople['profiles'][$rand]->sex);
                    }
                }

                $cache = [
                    'id' => $event['peer_id'],
                    'botActive' => date('Y-m-d H:i:s'),
                    'count' => intval($infoPeople['count']),
                    'profiles' => $infoPeople['profiles'],
                ];

                $conversation->setCache($cache);
                $bot->sendMessage($message);
            } else {
                $bot->sendMessage(TextMessage::getNeedRightAdminMessage());
            }
        } else {
            $bot->sendMessage(TextMessage::getLimitTimeMessage($bot->timeLimit()));
        }
    }

    private function runHelpBot(Bot $bot)
    {
        $bot->sendMessage(TextMessage::getHelpMessage());
    }

    private function runFirstInformation(Bot $bot)
    {
        $bot->sendMessage(TextMessage::getFirstInformationMessage());
    }

    private function runStatisticsAll(Bot $bot, array|Conversation $conversation, array $event)
    {
        $conversation = new Conversation($event['peer_id']);
        if (!$conversation->hasAccess()) {
            $bot->sendMessage(TextMessage::getNeedRightAdminMessage());
        } else {
            if ($conversation->hasCache()) {
                $infoPeople = $conversation->getCache();
                $people = $infoPeople['profiles'];
                usort($people, function ($a, $b) {
                    return strcmp($b['randomSelect'], $a['randomSelect']);
                });

                $message = TextMessage::getTopAllTimeMessage();
                for ($i = 0; $i < count($people); $i++) {
                    $message .= TextMessage::getPositionTopMessage($people[$i]['firstName'], $people[$i]['lastName'], $i, $people[$i]['randomSelect']);
                }
            } else {
                $message = TextMessage::getEmptyHistoryTimeMessage();
            }

            $bot->sendMessage($message);
        }
    }

    private function checkNewPeriod(Bot $bot, array $event)
    {
        $conversation = new Conversation($event['peer_id']);
        if ($conversation->hasAccess()) {
            if ($conversation->hasCache()) {
                $cacheConversation = $conversation->getCache();
                if (!isset($cacheConversation['lastWeek'])) {
                    $cacheConversation['lastWeek'] = date('W');
                    $cacheConversation['lastMonth'] = date('m');
                    $conversation->setCache($cacheConversation);
                }
                if ($cacheConversation['lastWeek'] !== date('W')) {
                    $people = $cacheConversation['profiles'];
                    usort($people, function ($a, $b) {
                        return strcmp($b['randomSelectWeek'], $a['randomSelectWeek']);
                    });

                    $message = TextMessage::getTopWeekMessage();
                    for ($i = 0; $i < count($people); $i++) {
                        $message .= TextMessage::getPositionTopMessage($people[$i]['firstName'], $people[$i]['lastName'], $i, $people[$i]['randomSelectWeek']);
                        $people[$i]['randomSelectWeek'] = 0;
                    }

                    $conversation->setCache(["profiles" => $people, 'lastWeek' => date('W')]);
                    $bot->sendMessage($message);
                }
                if ($cacheConversation['lastMonth'] !== date('m')) {
                    $people = $cacheConversation['profiles'];
                    usort($people, function ($a, $b) {
                        return strcmp($b['randomSelectMonth'], $a['randomSelectMonth']);
                    });

                    $message = TextMessage::getTopMonthMessage();
                    for ($i = 0; $i < count($people); $i++) {
                        $message .= TextMessage::getPositionTopMessage($people[$i]['firstName'], $people[$i]['lastName'], $i, $people[$i]['randomSelectMonth']);
                        $people[$i]['randomSelectMonth'] = 0;
                    }

                    $conversation->setCache(["profiles" => $people, 'lastMonth' => date('m')]);
                    $bot->sendMessage($message);
                }
            }
        } else {
            $bot->sendMessage(TextMessage::getNeedRightAdminMessage());
        }
    }

    private function runClear(Bot $bot, array $event)
    {
        $conversation = new Conversation($event['peer_id']);
        if ($conversation->hasAccess()) {
            if ($conversation->hasCache()) {
                $message = "";
                $conversation->updateAdminInfo();
                $cacheConversation = $conversation->getCache();
                foreach ($cacheConversation['profiles'] as $value) {
                    if ($value['id'] === $event['from_id'] && $value['isAdmin'] === "1") {
                        $message = TextMessage::getClearMessage($value['isAdmin']);
                        $conversation->deleteCache();
                    }
                    if ($value['id'] === $event['from_id'] && $value['isAdmin'] === "0") {
                        $message = TextMessage::getClearMessage($value['isAdmin']);
                    }
                }
            } else {
                $message = TextMessage::getClearMessage("-1");
                $conversation->deleteCache();
            }
            $bot->sendMessage($message);
        } else {
            $bot->sendMessage(TextMessage::getNeedRightAdminMessage());
        }
    }

    private function checkBigFail()
    {
        $randBigFail = rand(0, Config::BIG_FAIL_RANDOM);
        if ($randBigFail === 0)
            return true;
        else
            return false;
    }

    private function runBigFail()
    {
    }
}
