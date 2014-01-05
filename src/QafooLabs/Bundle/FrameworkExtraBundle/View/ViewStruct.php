<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle\View;

/**
 * View Model Base class.
 *
 * Target for properties and view logic passed to any templating mechanism
 * or serialization method. Returning a ViewStruct from a controller
 * is catched by the ViewListener and transformed into a Twig template
 * for example:
 *
 *      # View/Default/HelloView.php
 *      class HelloView extends ViewStruct
 *      {
 *          public $name;
 *
 *          public function reverseName()
 *          {
 *              return strrev($this->name);
 *          }
 *      }
 *
 *      # Controller/DefaultController.php
 *      class DefaultController
 *      {
 *          public function helloAction($name)
 *          {
 *              return new HelloView(array('name' => $name));
 *          }
 *      }
 *
 *      # Resources/views/Default/hello.html.twig
 *      Hello {{ view.name }} or {{ view.reverseName() }}!
 */
abstract class ViewStruct
{
    public function __construct(array $data)
    {
        foreach ($data as $property => $value) {
            if ( ! property_exists($this, $property)) {
                $this->throwPropertyNotExists($property);
            }

            $this->$property = $value;
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        $this->throwPropertyNotExists($name);
    }

    private function throwPropertyNotExists($property)
    {
        throw new \InvalidArgumentException(
            'View ' . get_class($this) . ' does not support property "$' . $property .
            ' The following properties exist: ' . implode(", ", array_keys(get_object_vars($this)))
        );
    }
}
