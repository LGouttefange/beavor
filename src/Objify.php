<?php

namespace Beavor;

/**
 * Class Objify
 * @static $truc
 */
class Objify
{
    public static function __callStatic($name, $arguments)
    {
        $selfInstance = new static();

        return $selfInstance->$name($arguments);
    }


    /**
     * @param string|object   $destination
     * @param \stdClass|array $source
     *
     * @return \stdClass
     */
    public function make($destination, $source)
    {
        if (is_string($destination)) {
            $destination = new $destination();
        }
        if (is_array($source)) {
            $source = json_decode(json_encode($source));
        }
        $sourceReflection = new \ReflectionObject($source);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            self::callPropertySetter($destination, $source, $sourceProperty);
        }

        return $destination;
    }

    /**
     * @param           $destination
     * @param \stdClass $source
     * @param           $sourceProperty
     */
    protected static function callPropertySetter($destination, \stdClass $source, $sourceProperty)
    {
        $name = $sourceProperty->getName();
        $upperCaseName = ucfirst($name); // holder -> Holder
        $setter = "set$upperCaseName"; // setHolder
        if (method_exists($destination, $setter)) {
            $destination->$setter($source->$name); // $destination->setHolder($source->holder)

            return;
        }
        $destinationReflection = new \ReflectionClass($destination);
        if (false === $destinationReflection->hasProperty($name)) {
            return;
        }
        $destinationProperty = $destinationReflection->getProperty($name);
        if (false === $destinationProperty->isPublic()) {
            return;
        }

        if (false === $destinationProperty->isPublic()) {
            return;
        }

        $destination->$name = $source->$name;
    }

}