<?php

namespace vkbot_conversation\classes;

use vkbot_conversation\classes\bot\BotCommands;
use vkbot_conversation\classes\message\MessageEvent;

require_once(__DIR__."/../Config.php");

class Event
{
    const TYPE_MESSAGE_FOR_BOT = "MESSAGE_FOR_BOT";
    const TYPE_CHAT_EVENT = "CHAT_EVENT";

    private string|null $typeEvent = null;

    public function __construct(private MessageEvent $eventData, private BotCommands $botCommands)
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
            if (stripos($text, "/") !== false && (stripos($text, strtoupper(\BOT_NAME_VK_CONVERSATION)) !== false || stripos($text, strtolower(\BOT_NAME_VK_CONVERSATION)) !== false)) {
                $command = substr($text, stripos($text, "/"));
                if (in_array($command, $this->botCommands->getCommandsName()))
                    return true;
                else
                    return false;
            } else
                return false;
        } else
            return false;
    }

    private function getCommand(string $text): ?string
    {
        $isCommand =  substr($text, stripos($text, "/"));
        if (in_array($isCommand, $this->botCommands->getCommandsName())) {
            $fullCommand = false;
            foreach ($this->botCommands->getCommandsName() as $command) {
                if ($command === $isCommand) {
                    $isCommand = $command;
                    $fullCommand = true;
                }
            }
            if ($fullCommand)
                return $isCommand;
            else
                return null;
        } else
            return null;
    }

    private function isChatEvent(MessageEvent $message): bool
    {
        if ($message->getAction())
            return true;
        else
            return false;
    }

    public function runEvent()
    {   
        switch ($this->typeEvent) {
            case Event::TYPE_MESSAGE_FOR_BOT:
                $command = $this->getCommand($this->eventData->getText());
                if ($command)
                    $this->botCommands->runBotCommand($this->eventData, $command);
                break;
            case Event::TYPE_CHAT_EVENT:
                $this->botCommands->runBotChatEvent($this->eventData);
                break;
        }
    }
}
