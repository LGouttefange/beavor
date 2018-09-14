<?php


namespace Helper;


use Beavor\Helpers\CollectionInterface;

class DummyClassCollection implements CollectionInterface
{
    /** @var DummyClass[] */
    public $objects = [];

    public function getEntries()
    {
        return $this->objects;
    }

    public function setEntries(array $entries)
    {
        $this->objects = $entries;
    }

    public function getEntriesClass()
    {
        return DummyClass::class;
    }
}