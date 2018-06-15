<?php


namespace Beavor\Actions;


use Beavor\Objify;
use PhpDocReader\PhpDocReader;
use ReflectionParameter;
use ReflectionProperty;

class UseSetter extends AbstractAction
{
    protected $setter;

    public function canHandle()
    {
        $upperCaseName = ucfirst($this->propertyName); // holder -> Holder
        $this->setter = "set$upperCaseName"; // setHolder
        return method_exists($this->destination, $this->setter);
    }

    /**
     * @throws \ReflectionException
     * @throws \PhpDocReader\AnnotationException
     */
    public function doIt()
    {
        $reader = new PhpDocReader();
        $setter = $this->setter;

        $sourceValue = $this->sourceValueWIthCastIfNecessary();

        $this->destination->$setter($sourceValue); // $destination->setHolder($source->holder)
    }


}