<?php

namespace vkbot_conversation\models;

use vkbot_conversation\utils\FileWorker;
use vkbot_conversation\utils\Converter;

abstract class DataModel
{

    public function __construct(
        protected string $pathStorage,
        protected string $filename
    ) {
    }

    public function hasData(): bool
    {
        if (empty(FileWorker::readData($this->pathStorage . "/" . $this->filename)))
            return false;
        else
            return true;
    }

    public function getData(): object|null
    {
        $data = FileWorker::readData($this->pathStorage . "/" . $this->filename);
        if (!empty($data))
            return $data;
        else
            return null;
    }

    public function setData(object $data)
    {
        FileWorker::writeData($this->pathStorage . "/" . $this->filename, $data);
    }

    public function setKeyValue(string $key, string|array $value)
    {
        if ($this->hasData()) {
            $data = $this->getData();
            if (is_string($value))
                $data->{$key} = $value;
            if (is_array($value))
                $data->{$key} = Converter::arrayToObject($value);
            FileWorker::writeData($this->pathStorage . "/" . $this->filename, $data);
        }
    }

    public function deleteData()
    {
        file_put_contents($this->pathStorage . "/" . $this->filename, "");
    }
}
