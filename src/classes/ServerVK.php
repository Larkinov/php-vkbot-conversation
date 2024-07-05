<?php

namespace vkbot_conversation\classes;

use vkbot_conversation\classes\data\ServerData;
use vkbot_conversation\classes\message\Action;
use vkbot_conversation\classes\message\MessageEvent;
use vkbot_conversation\utils\Converter;

class ServerVK
{

    private ServerData $data;
    private string $wait = "25";
    // private string $mode = "2";
    // private string $version = "3";
    static string $nameBotStatic;

    public function __construct(
        private string $token,
        private string $version,
        private string $groupId,
        string $storagePathCache,
        string $filename,
        private string $nameBot
    ) {
        $this->data = new ServerData($storagePathCache, $filename);
        self::$nameBotStatic = $nameBot;
    }

    public function connection()
    {
        if (!$this->data->hasData())
            $this->getServer();
    }


    private function getServer()
    {
        $request_params = [
            'access_token' => $this->token,
            'group_id' => $this->groupId,
            'v' => $this->version
        ];

        $request_url = 'https://api.vk.com/method/groups.getLongPollServer?' . http_build_query($request_params);
        $response = file_get_contents($request_url);

        if ($response !== false) {
            $response_data = json_decode($response, true);
            if (isset($response_data['response'])) {
                if (isset($response_data['response']['key']) && isset($response_data['response']['server']) && isset($response_data['response']['ts'])) {
                    $this->data->setData(Converter::arrayToObject($response_data['response']));
                } else
                    throw new \Exception("error send message: key, server, ts not found");
            } else
                throw new \Exception("error send message");
        } else
            throw new \Exception("error send request");
    }

    function getEventsData(): array
    {
        $urlServer = $this->data->getUrlServer();
        $key = $this->data->getKey();
        $ts = $this->data->getTs();

        // $request_url = "$urlServer?act=a_check&key=$key&ts=$ts&wait=$this->wait&mode=2&version=3";
        $request_url = "$urlServer?act=a_check&key=$key&ts=$ts&wait=$this->wait";
        $response = file_get_contents($request_url);

        if ($response !== false) {
            $response_data = json_decode($response, true);
            if (isset($response_data['failed'])) {
                switch ($response_data['failed']) {
                    case "1":
                        //история событий устарела или была частично утеряна, приложение может получать события далее, используя новое значение ts из ответа
                        // $this->getServer();
                        break;
                    case "2":
                        // истекло время действия ключа, нужно заново получить key методом groups.getLongPollServer.
                        $this->getServer();
                        break;
                    case "3":
                        // информация утрачена, нужно запросить новые key и ts методом groups.getLongPollServer.
                        $this->getServer();
                        break;
                    default:
                        throw new \Exception("Error response failed: $response_data[failed] - unknown failed error");
                }
            }
            if (isset($response_data['ts'])) {
                $this->data->setTs($response_data['ts']);
                $messageForBot = $this->checkVKEventsNewMessage($response_data);
                if ($messageForBot)
                    return $messageForBot;
            }
        } else
            throw new \Exception("error response data");
        return [];
    }

    private function checkVKEventsNewMessage(array $events): array
    {
        $messages = [];
        foreach ($events['updates'] as $event) {
            if (isset($event['type'])) {
                if ($event['type'] === "message_new") {
                    $m = $event['object']['message'];
                    if ($m['action'])
                        $a = new Action(strtoupper($m['action']['type']), $m['action']['member_id'], $m['action']['text']);
                    else
                        $a = null;
                    array_push($messages, new MessageEvent($m['id'], $m['date'], $m['peer_id'], $m['from_id'], $m['text'], $m['members_count'], $a));
                }
            }
        }

        echo "<br><br>";
        return $messages;
    }
}
