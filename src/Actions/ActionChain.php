<?php


namespace Beavor\Actions;


use PhpDocReader\AnnotationException;

class ActionChain
{
    /** @var ActionInterface[] */
    protected $actions;

    public function __construct($actions)
    {
        $this->actions = $actions;
    }


    public function handle($source, $destination, $sourceProperty)
    {
        /** @var ActionInterface $action */
        $action = current(array_filter($this->actions, function (ActionInterface $action) use ($sourceProperty, $destination, $source) {
            return $action->canHandle();
        }));
        if (!$action) {
            return;
        }
        try {
            $action->doIt();
        } catch (AnnotationException $e) {
            // do nothing
        } catch (\ReflectionException $e) {
            // do nothing
        }
    }
}