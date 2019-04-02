<?php
/**
 * Created by PhpStorm.
 * User: Loïc Gouttefangeas <loic.gouttefangeas.pro@gmail.com>
 * Date: 25/11/2018
 * Time: 19:04
 */

namespace Beavor\Actions;



use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

class BuildClass {

    /** @var ClassType[] */
    private $classes = [];

    public function buildRootClass($data, $className, $namespaceName)
    {
        $class = new ClassType($className, new PhpNamespace($namespaceName));
        $class->addComment("Auto generated Beavor DTO class");
        $this->buildClass($data, $class, new PhpNamespace($class->getNamespace()->getName() . "\\" . $class->getName()));
        return $this->classes;
    }

    /**
     * @param           $data
     * @param ClassType $class
     * @param           $property
     */
    public function buildClass($data, ClassType $class, $namespace = null)
    {
        foreach ($data as $propertyName => $value) {
            $this->buildProperty($class, $namespace, $propertyName, $value);
        }
        $this->classes[] = $class;
    }


    /**
     * @param $class
     * @param $namespace
     * @param $propertyName
     * @param $value
     */
    protected function buildProperty(ClassType $class, PhpNamespace $namespace, $propertyName, $value)
    {
        // not a property
        if (!is_string($propertyName)) {
            return;
        }
        $class->addProperty($propertyName)->setVisibility('public');
        $getter = $class->addMethod("get".ucfirst($propertyName));
        $getter->setBody("return \$this->$propertyName;");

        // no more action is needed
        if (!is_array($value)) {
            return;
        }
        $class->getProperty($propertyName)->setValue([]);
        //we dont know what it has, so we default to a simple array
        if (empty($value)) {
            $class->getProperty($propertyName)->addComment("@var array");
            return;
        }
        $nestedClassName = ucfirst($propertyName . "Item");
        $newNestedClass = new ClassType($nestedClassName, $namespace ?: $class->getNamespace());
        $newNameSpace = new PhpNamespace($namespace->getName() ?: $class->getNamespace()->getName() . "\\" . $nestedClassName);
        $newClassFullName = "\\" . $newNameSpace->getName() . "\\" . $nestedClassName;
        // Class collection Has Many
        if ((array_key_exists(0, $value))) {
            if (is_array($value[0])) {
                $this->buildProperty($newNestedClass,$newNameSpace, $propertyName, $value[0]);
                $class->getProperty($propertyName)->addComment("@var ${newClassFullName}[]");
                $getter->addComment("@return ${newClassFullName}[]");
                $this->buildClass($value[0], $newNestedClass, $newNameSpace);
                return;
            }
        }

        // class property Has One
        $class->getProperty($propertyName)->addComment("@var $newClassFullName");
        $getter->addComment("@return $newClassFullName");
        $this->buildClass($value, $newNestedClass, $newNameSpace);
    }

}