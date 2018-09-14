<?php


namespace Beavor\Helpers;


interface CollectionInterface
{
    public function getEntriesClass();
    public function setEntries(array $entries);
}