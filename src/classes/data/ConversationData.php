<?php

namespace vkbot_conversation\classes\data;

use vkbot_conversation\models\DataModel;

class ConversationData extends DataModel
{
    public function __construct(string $storagePath, string $filename) {
        parent::__construct($storagePath, $filename);
    }
}
