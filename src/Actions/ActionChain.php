<?php


namespace Beavor\Actions;


use PhpDocReader\AnnotationException;

class ActionChain
{
    /** @var string[] */
    protected $actions;

    public function __construct($actions)
    {
        $this->actions = $actions;
    }


    public function handle($source, $destination, $sourceProperty)
    {

        $actions = array_map(function ($action) use ($sourceProperty, $destination, $source) {
            /** @var ActionInterface $actionInstance */
            return new $action($source, $destination, $sourceProperty);
        }, $this->actions);

        /** @var ActionInterface $action */
        $action = current(array_filter($actions, function (ActionInterface $action) {
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