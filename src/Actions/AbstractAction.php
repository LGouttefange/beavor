<?php


namespace Beavor\Actions;


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
     * @param ReflectionClass $destinationReflection
     * @param $propertyName
     * @param $reader
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
     * @return ReflectionClass
     * @throws \ReflectionException
     */
    protected function getDestinationReflexion()
    {
        return new ReflectionClass($this->destination);
    }
}