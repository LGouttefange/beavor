<?php

namespace Beavor;
use Beavor\Actions\ActionChain;
use Beavor\Actions\ActionInterface;
use Beavor\Actions\UsePublicProperty;
use Beavor\Actions\UseSetter;
use Beavor\Helpers\SanitizedSourceString;
use PhpDocReader\PhpDocReader;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Class Objify
 * @method static makeStatic(string|object $destination, string|array|object $source)
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
     * @param string $source
     *
     * @return \stdClass
     */
    public function fromRawJson($destination, $source)
    {
        $data = json_decode($source);
        if($data === null){
            throw new \InvalidArgumentException("Provided JSON is not valid");
        }
        return $this->make($destination, $data);
    }

    public function fromRawXml($destination, $source)
    {
        $data = simplexml_load_string((new SanitizedSourceString($source))->getValue());

        $json  = json_encode($data);
        $xmlArr = json_decode($json, true);

        return $this->make($destination, $xmlArr);

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
        if(is_array($source)){
            return $destination;
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