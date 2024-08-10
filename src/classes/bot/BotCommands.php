<?php

namespace vkbot_conversation\classes\bot;

use vkbot_conversation\classes\message\MessageEvent;

class BotCommands
{

    static array $commandsChatEvent = [""];
    static array $commands = [""];
    static array $commandsName = [""];

    public static function registerNewCommand(string $nameNewCommand, callable $newCommand)
    {
        array_push(BotCommands::$commandsName,$nameNewCommand);
        BotCommands::$commands[$nameNewCommand] = $newCommand;
    }

    public static function registerNewCommandChatEvent(string $nameNewCommand, callable $newCommand)
    {
        BotCommands::$commandsChatEvent[$nameNewCommand] = $newCommand;
    }

    public static function runBotCommand(MessageEvent $message, string $nameCommand)
    {
        foreach (BotCommands::$commands as $key => $value) {
            if($key===$nameCommand)
                call_user_func_array($value,[$message]);
        }
    }
    public static function runBotChatEvent(MessageEvent $message)
    {
        foreach (BotCommands::$commandsChatEvent as $key => $value) {
            if($key===$message->getAction()->getType())
                call_user_func_array($value,[$message]);
        }
    }
}
