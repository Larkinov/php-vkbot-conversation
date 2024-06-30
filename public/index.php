<?php

namespace vkbot;

require_once(__DIR__ . "/../autoload.php");

use vkbot\classes\Event;
use vkbot\classes\ServerVK;
use vkbot\utils\WriterLogs;
use vkbot\config\Config;
use vkbot\classes\MainScheme;


try {

    $lockFile = __DIR__ . '/script.lock';

    $fp = fopen($lockFile, 'w');
    if (!flock($fp, LOCK_EX | LOCK_NB)) {
        die('Script is already running.');
    }


    $server = new ServerVK(Config::TOKEN, Config::VERSION, Config::GROUP_ID);

    if ($server->connection()) {

        $initEvent = new Event($server->getKey(), $server->getUrlServer(), $server->getTs());
        $events = $initEvent->getEvent();

        if (is_array($events)) {
            foreach ($events['updates'] as $event) {
                $mainScheme = new MainScheme($event);
                $mainScheme->start();
            }
        }
        if (is_string($event)) {
            $server->getServer();
        }
        if (is_bool($event)) {
            throw new \Exception("error in get event");
        }
    } else
        throw new \Exception("error get server vk");

    flock($fp, LOCK_UN);
    fclose($fp);
    unlink($lockFile);

} catch (\Throwable $th) {
    echo (new WriterLogs())->writeErrorLog($th, "index.php");
    
    flock($fp, LOCK_UN);
    fclose($fp);
    unlink($lockFile);
}
