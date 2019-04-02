<?php

namespace Beavor;

use Beavor\Actions\ActionChain;
use Beavor\Actions\ActionInterface;
use Beavor\Actions\UsePublicProperty;
use Beavor\Actions\UseSetter;
use Beavor\Helpers\ArrayToXml;
use Beavor\Helpers\CollectionInterface;
use Beavor\Helpers\SanitizedSourceString;
use Beavor\Helpers\SourceDataDeserializer;
use PhpDocReader\PhpDocReader;
use Psr\Http\Message\ResponseInterface;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Class Objify
 * @method static makeStatic(string | object $destination, string | array | object $source)
 */
class Objify
{
    public static function __callStatic($name, $arguments)
    {
        $selfInstance = new static();

        return $selfInstance->$name($arguments);
    }


    /**
     * @param string|object $destination
     * @param string $source
     *
     * @return \stdClass
     */
    public function fromRawJson($destination, $source)
    {
        $data = json_decode($source);
        if ($data === null) {
            throw new \InvalidArgumentException("Provided JSON is not valid");
        }

        return $this->make($destination, $data);
    }

    public function fromRawXml($destination, $source)
    {
        $data = ArrayToXml::convert((new SanitizedSourceString($source))->getValue());

        $json = json_encode($data);
        $xmlArr = json_decode($json, true);

        return $this->make($destination, $xmlArr);

    }

    /**
     * @param string|object $destination
     * @param \stdClass|array $source
     *
     * @return \stdClass
     */
    public function make($destination, $source)
    {
        if (!isset($this)) {
            return (new static())->make($destination, $source);
        }

        if (is_string($destination)) {
            $destination = new $destination();
        }

        $source = (new SourceDataDeserializer())->deserialize($source);

        if (is_array($source) && $destination instanceof CollectionInterface) {
            $destination->setEntries($this->makeCollection($destination->getEntriesClass(), $source));
            return $destination;
        }

        $this->fillClass($destination, $source);

        return $destination;
    }


    public function makeCollection($destination, $source)
    {
        $source = (new SourceDataDeserializer())->deserialize($source);
        return array_map(function ($entry) use ($destination) {
            return $this->fillClass(new $destination(), (new SourceDataDeserializer())->deserialize($entry));
        }, $source);
    }

    /**
     * @param           $destination
     * @param \stdClass $source
     * @param           $sourceProperty
     */
    protected function callPropertySetter($destination, \stdClass $source, $sourceProperty)
    {
        (new ActionChain([
            UseSetter::class,
            UsePublicProperty::class,
        ]))->handle($source, $destination, $sourceProperty);

    }

    /**
     * @param $destination
     * @param $source
     */
    protected function fillClass($destination, $source)
    {
        if(is_array($source)){
            return $destination;
        }
        $sourceProperties = (new \ReflectionObject($source))->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $this->callPropertySetter($destination, $source, $sourceProperty);
        }

        return $destination;
    }


}