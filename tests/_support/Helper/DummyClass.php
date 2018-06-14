<?php


namespace Helper;


use Beavor\Helpers\Arrayable;

class DummyClass
{
    use Arrayable;

    public $dummyProperty;
    protected $dummySetterProperty;
    private $unaccessibleProperty;

    /**
     * @return mixed
     */
    public function getDummySetterProperty()
    {
        return $this->dummySetterProperty;
    }

    /**
     * @param mixed $dummySetterProperty
     */
    public function setDummySetterProperty($dummySetterProperty)
    {
        $this->dummySetterProperty = $dummySetterProperty;
    }

    /**
     * @return mixed
     */
    public function getUnaccessibleProperty()
    {
        return $this->unaccessibleProperty;
    }
}