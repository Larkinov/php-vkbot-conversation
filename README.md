# php-vkbot-conversation
this is a small library for working with the vk bot for conversations

# example

Command::addNewCommand(['/новая_команда_1']);

$token = "token_group_vk";
$version = "5.199";
$group = "id_group_vk";
$server = new ServerVK($token, $version, $group, "/path/to/storage", "name_file_server","name_bot");

$server->connection();
$eventsData = $server->getEventsData();

foreach ($eventsData as $eventData) {
    $event = new Event($eventData);
    $event->runBot();
}