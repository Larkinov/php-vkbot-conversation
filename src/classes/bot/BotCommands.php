<?php

namespace vkbot_conversation\classes\bot;

use vkbot_conversation\classes\message\MessageEvent;

class BotCommands
{
    private array $commands = [""];
    private array $commandsNameChatEvent = [""];
    private array $commandsName = [""];

    public function registerNewCommand(string $nameNewCommand, callable $newCommand)
    {
        array_push($this->commandsName,$nameNewCommand);
        $this->commands[$nameNewCommand] = $newCommand;
    }

    public function registerNewCommandChatEvent(string $nameNewCommand, callable $newCommand)
    {
        $this->commandsNameChatEvent[$nameNewCommand] = $newCommand;
    }

    public function runBotCommand(MessageEvent $message, string $nameCommand)
    {
        foreach ($this->commands as $key => $value) {
            if($key===$nameCommand)
                call_user_func_array($value,[$message]);
        }
    }
    public function runBotChatEvent(MessageEvent $message)
    {
        foreach ($this->commandsNameChatEvent as $key => $value) {
            if($key===$message->getAction()->getType())
                call_user_func_array($value,[$message]);
        }
    }

    public function getCommandsName():array{
        return $this->commandsName;
    }
    public function getCommandsNameChatEvent():array{
        return $this->commandsNameChatEvent;
    }
}
