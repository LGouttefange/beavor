<?php


namespace Helper;


use Beavor\Helpers\Arrayable;

class DummyClass
{
    use Arrayable;

    public $dummyProperty;
    protected $dummySetterProperty;
    private $unaccessibleProperty;
    /** @var DummyClass */
    public $nestedProperty;
    protected $nestedSetterProperty;
    /** @var DummyClass */
    protected $nestedSetterDocProperty;

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

    /**
     * @param DummyClass $nestedSetterProperty
     */
    public function setNestedSetterProperty($nestedSetterProperty)
    {
        $this->nestedSetterProperty = $nestedSetterProperty;
    }

    /**
     * @return mixed
     */
    public function getNestedSetterProperty()
    {
        return $this->nestedSetterProperty;
    }

    /**
     * @return DummyClass
     */
    public function getNestedSetterDocProperty()
    {
        return $this->nestedSetterDocProperty;
    }

    public function setNestedSetterDocProperty($nestedSetterDocProperty)
    {
        $this->nestedSetterDocProperty = $nestedSetterDocProperty;
    }
}