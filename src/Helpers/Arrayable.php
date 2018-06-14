<?php


namespace Beavor\Helpers;


trait Arrayable
{
    public function toArray()
    {
        return get_object_vars($this);
    }
}