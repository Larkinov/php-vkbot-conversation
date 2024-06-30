<?php

namespace vkbot\classes;

class Profile
{
    public function __construct(
        public $id,
        public $sex,
        public $firstName,
        public $lastName,
        public $randomSelectMonth,
        public $randomSelectWeek,
        public $randomSelect,
        public $isAdmin,
    ) {
    }
}
