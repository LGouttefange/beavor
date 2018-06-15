<?php


namespace Beavor\Actions;


use Beavor\Objify;
use PhpDocReader\PhpDocReader;
use ReflectionClass;
use ReflectionProperty;

abstract class AbstractAction implements ActionInterface
{
    protected $propertyName;
    protected $source;
    protected $destination;
    /** @var ReflectionProperty */
    protected $sourceProperty;

    public function __construct($source, $destination, ReflectionProperty $sourceProperty)
    {
        $this->source = $source;
        $this->destination = $destination;
        $this->sourceProperty = $sourceProperty;
        $this->propertyName = $sourceProperty->getName();
    }

    /**
     * @param $destinationReflection
     *
     * @return ReflectionProperty
     * @throws \ReflectionException
     */
    protected function getDestinationProperty()
    {
        return ($this->getDestinationReflexion())->getProperty($this->propertyName);
    }

    /**
     *
     * @throws \ReflectionException
     * @throws \PhpDocReader\AnnotationException
     */
    protected function getFieldClass()
    {
        $reader = new PhpDocReader();
        // Read a property type (@var phpdoc)
        $property = new ReflectionProperty($this->getDestinationReflexion()->getName(), $this->propertyName);

        return $reader->getPropertyClass($property);
    }

    /**
     * @return array|\stdClass
     * @throws \PhpDocReader\AnnotationException
     * @throws \ReflectionException
     */
    protected function sourceValueWIthCastIfNecessary()
    {
        $propertyName = $this->propertyName;
        $sourceValue = $this->source->$propertyName;
        if (is_array($sourceValue) && $this->getClassTypeOfCollectionField()) {
            $sourceValue = array_map(function ($element) {
                return (new Objify)->make($this->getClassTypeOfCollectionField(), $element);
            }, $sourceValue);
        } elseif ($this->getFieldClass()) {
            $sourceValue = (new Objify)->make($this->getFieldClass(), $sourceValue);
        }

        return $sourceValue;
    }
    /**
     * @return null
     * @throws \ReflectionException
     */
    protected function getClassTypeOfCollectionField()
    {
        $reader = new PhpDocReader();
        // Read a property type (@var phpdoc)
        $property = new ReflectionProperty($this->getDestinationReflexion()->getName(), $this->propertyName);

        // Get the content of the @var annotation
        if (preg_match('/@var\s+([^\s]+)/', $property->getDocComment(), $matches)) {
            list(, $type) = $matches;
        } else {
            return null;
        }
        $type = preg_replace('/\[\]/',"",$type);
        return  class_exists($type) ? $type : null;
    }

    /**
     * @return ReflectionClass
     * @throws \ReflectionException
     */
    protected function getDestinationReflexion()
    {
        return new ReflectionClass($this->destination);
    }
}