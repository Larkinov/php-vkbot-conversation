<?php

namespace vkbot_conversation\classes\profile;

use vkbot_conversation\classes\interface\AddedInfo;
use vkbot_conversation\classes\profile\ProfileData;

class Profile implements AddedInfo
{
    private string $first_name;
    private string $last_name;
    private string $sex;
    private bool $isAdmin;
    private string $filename;
    private ProfileData $data;

    public function __construct(private string $id, private string $peer_id, private string $storagePath="") {
        $this->filename=\BOT_NAME_VK_CONVERSATION ."_cv_". $peer_id . "_pf_$id";
        $this->filename = strtolower($this->filename);
        $this->data = new ProfileData($storagePath,$this->filename);
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }
    public function getId(): string
    {
        return $this->id;
    }
    public function setFirstName(string $first_name)
    {
        $this->first_name = $first_name;
    }
    public function getFirstName(): string
    {
        return $this->first_name;
    }
    public function setLastName(string $last_name)
    {
        $this->last_name = $last_name;
    }
    public function getLastName(): string
    {
        return $this->last_name;
    }
    public function setSex(string $sex)
    {
        $this->sex = $sex;
    }
    public function getSex(): string
    {
        return $this->sex;
    }
    public function setIsAdmin(bool $isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }
    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function saveInfo(string $key, string | array | object $value){
        $this->data->setKeyValue($key, $value);
    }
    public function getSavedInfo(string $key){
        return $this->data->getKeyValue($key);
    }

    public function clearInfo(){
        $this->data->clearData();
    }

    public function getSavedFullInfo(){
        return $this->data->getData();
    }

    public function deleteInfo(string $key){
        $data = $this->getSavedFullInfo();
        unset($data->{$key}); 
        $this->data->setdata($data);
    }
}
