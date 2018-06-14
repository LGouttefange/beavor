<?php


namespace Beavor\Actions;


use Beavor\Objify;
use PhpDocReader\PhpDocReader;
use ReflectionClass;
use ReflectionProperty;

class UsePublicProperty extends AbstractAction
{
    public function canHandle()
    {
        try{
            $destinationReflection = $this->getDestinationReflexion();
            if (false === $destinationReflection->hasProperty($this->propertyName)) {
                return false;
            }

            return $this->getDestinationProperty()->isPublic();
        }catch (\ReflectionException $e) {
            return false;
        }
    }

    /**
     * @throws \ReflectionException
     * @throws \PhpDocReader\AnnotationException
     */
    public function doIt()
    {
        $propertyName = $this->propertyName;
        $sourceValue = $this->source->$propertyName;

        $fieldClass = $this->getFieldClass();
        if($fieldClass){
            $sourceValue  = (new Objify)->make($fieldClass, $sourceValue);
        }
        $this->destination->$propertyName = $sourceValue;

    }
}