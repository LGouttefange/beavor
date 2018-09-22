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
        $setter = $this->setter;
        $sourceValue = $this->sourceValueWIthCastIfNecessary();

        $this->destination->$setter($sourceValue); // $destination->setHolder($source->holder)
    }

    protected function getFieldClass()
    {
        $varAnnotationClass = parent::getFieldClass();
        if($varAnnotationClass !== null){
            return $varAnnotationClass;
        }
        return $this->getSetterParameterClass();
    }

    private function getSetterParameterClass()
    {
        $reader = new PhpDocReader();
        $reflectedSetter = new \ReflectionParameter([$this->destination, $this->setter], $this->propertyName);
        return  $reader->getParameterClass($reflectedSetter);
    }


}