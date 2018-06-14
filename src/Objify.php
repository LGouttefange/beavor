<?php

namespace Beavor;
use Beavor\Actions\ActionChain;
use Beavor\Actions\UsePublicProperty;
use Beavor\Actions\UseSetter;
use PhpDocReader\PhpDocReader;
use ReflectionParameter;
use ReflectionProperty;

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
        $sourceProperties = (new \ReflectionObject($source))->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $this->callPropertySetter($destination, $source, $sourceProperty);
        }

        return $destination;
    }

    /**
     * @param           $destination
     * @param \stdClass $source
     * @param           $sourceProperty
     */
    protected function callPropertySetter($destination, \stdClass $source, $sourceProperty)
    {
        $actionClasses = [
            UseSetter::class,
            UsePublicProperty::class,
        ];

        $actions = array_map(function($action) use ($sourceProperty, $destination, $source) {
            /** @var ActionInterface $actionInstance */
            return new $action($source, $destination, $sourceProperty);
        }, $actionClasses);

        (new ActionChain($actions))->handle($source, $destination, $sourceProperty);

    }



}