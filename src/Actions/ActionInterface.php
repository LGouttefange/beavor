<?php


namespace Beavor\Actions;


interface ActionInterface
{
    public function canHandle();


    /**
     * @throws \ReflectionException
     * @throws \PhpDocReader\AnnotationException
     */
    public function doIt();
}