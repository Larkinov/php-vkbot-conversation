# php-vkbot-conversation
Это небольшая библиотека облегчающая работу с беседами "ВКонтакте".

# Необходимые условия для старта библиотеки
Вам необходимо иметь необходимый минимум для начала работы:
1. Сервер с пользователем с разрешениями на изменение владельцев и прав каталогов/файлов;
2. Сообщество в "ВКонтакте" с разрешениями для работы с ботом (см. https://dev.vk.com/ru/api/bots/getting-started);
3. Ключ от этого сообщества; версия библиотеки, которая работает с ботом; id своего сообщества;

# ВНИМАНИЕ
На нынешний момент протестировано только для версии 5.199. Также возможны какие-то баги/неполадки/пожелания, об этом писать в issues. 

# config
define("TOKEN_VK_CONVERSATION", "your_token"); - ключ от сообщества ВК
define("VERSION_VK_CONVERSATION", "your_version") - версия библиотеки подключенная в сообществе
define("GROUP_VK_CONVERSATION", "your_id_community") - id сообщества
define("BOT_NAME_VK_CONVERSATION","your_name_bot") - имя бота (ВНИМАНИЕ, ИМЕННО НА ЭТО ИМЯ БОТ БУДЕТ РЕАГИРОВАТЬ)
define("ROOT_PATH_STORAGE_VK_CONVERSATION","your_root_path") - корневая папка где будет храниться сохраненная информация

# example отправка сообщений
```php
require_once(__DIR__ . "/autoload.php");

use vkbot_conversation\classes\bot\Bot;
use vkbot_conversation\classes\bot\BotCommands;
use vkbot_conversation\classes\conversation\Conversation;
use vkbot_conversation\classes\server\ServerVK;
use vkbot_conversation\classes\Event;
use vkbot_conversation\classes\message\MessageEvent;

$server = new ServerVK("server");

$func = function (MessageEvent $message) {
    $bot = new Bot();
    $bot->sendMessage("bot message",$message->getPeerId());
};  

$bc = new BotCommands();
$bc->registerNewCommand('/your_command', $func);

$server->connection();
$eventsData = $server->getEventsData();
foreach ($eventsData as $eventData) {
    $event = new Event($eventData, $bc);
    $event->runEvent();
}
```

**вызов бота в ВКонтакте**
your_name_bot /your_command

**сообщение бота**
bot message