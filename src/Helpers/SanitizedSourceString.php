<?php


namespace Beavor\Helpers;


class SanitizedSourceString
{
    protected $value;

    public function __construct($source)
    {
        $this->value = preg_replace("/\n?\r?/", "", $source);
        $this->value = preg_replace("/(<\/?)([a-z]+)?:([^>]*>)/", "$1$3", $this->value);
        $this->value = str_replace("-", "_", $this->value);
    }


    public function getValue()
    {
        return $this->value;
    }
}