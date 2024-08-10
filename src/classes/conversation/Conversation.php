<?php

namespace vkbot_conversation\classes\conversation;

use vkbot_conversation\classes\conversation\ConversationData;

require_once(__DIR__ . "/../../Config.php");

trait ConversationInfo
{
    static public function getPeopleConversation(string $peer_id)
    {
        $request_params = [
            'access_token' => TOKEN_VK_CONVERSATION,
            'v' => VERSION_VK_CONVERSATION,
            'peer_id' => $peer_id,
        ];

        $request_url = 'https://api.vk.com/method/messages.getConversationMembers?' . http_build_query($request_params);
        $response = file_get_contents($request_url);

        if ($response !== false) {
            $response_data = json_decode($response, true);
            if (isset($response_data['response'])) {
                $profiles = [];
                // foreach ($response_data['response']['profiles'] as $value) {
                //     $profile = new Profile($value['id'], $value['sex'], $value['first_name'], $value['last_name'], 0, 0, 0, 0);
                //     array_push($profiles, $profile);
                // }
                // $infoPeople = [
                //     "count" => $response_data['response']['count'],
                //     "profiles" => $profiles,
                // ];
                print_r($response_data['response']);
                return $response_data['response'];
            } else {
                throw new \Exception("$response_data[error][error_msg]");
            }
        } else {
            throw new \Exception("error send message - $response");
        }
    }
}

class Conversation
{

    
    public function __construct(private string $peer_id) {}

    public function getPeopleConversation()
    {
        ConversationInfo::getPeopleConversation($this->peer_id);
    }
}
