<?php

namespace vkbot_conversation\classes;

use vkbot_conversation\classes\bot\Command;
use vkbot_conversation\classes\message\MessageEvent;
use vkbot_conversation\classes\ServerVK;

class Event
{
    const TYPE_MESSAGE_FOR_BOT = "MESSAGE_FOR_BOT";
    const TYPE_CHAT_EVENT = "CHAT_EVENT";

    private string|null $typeEvent = null;

    public function __construct(private MessageEvent $eventData)
    {
        if ($this->isMessageForBot($eventData))
            $this->typeEvent = Event::TYPE_MESSAGE_FOR_BOT;
        if ($this->isChatEvent($eventData))
            $this->typeEvent = Event::TYPE_CHAT_EVENT;
    }

    private function isMessageForBot(MessageEvent $message): bool
    {
        $text = $message->getText();
        if ($text) {
            if (stripos($text, "/") !== false && stripos($text, ServerVK::$nameBotStatic) !== false) {
                $command = substr($text, stripos($text, "/"));
                if (in_array($command, Command::$commands))
                    return true;
                else
                    return false;
            } else
                return false;
        } else
            return false;
    }

    private function getCommand(string $text): string
    {
        return substr($text, stripos($text, "/"));
    }

    private function isChatEvent(MessageEvent $message): bool
    {
        if ($message->getAction())
            return true;
        else
            return false;
    }

    public function runBot()
    {
        switch ($this->typeEvent) {
            case Event::TYPE_MESSAGE_FOR_BOT:
                Command::runBotCommand($this->getCommand($this->eventData->getText()));
                break;
            case Event::TYPE_CHAT_EVENT:
                Command::runBotChatEvent();
                break;
            default:
                echo "unknown type event";
        }
        print_r($this->eventData);
    }
}
