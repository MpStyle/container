<?php

namespace mpstyle\container;

use ReflectionException;

/**
 * Lazy and naive container for the dependency injection.<br>
 * It implements singleton pattern.<br>
 * <br>
 * Usages:
 * <code>
 * interface Foo {}
 * class Dummy {}
 * class Bar implements Foo {
 *     public $dummy;
 *
 *     public function __construct(Dummy $d){ $this->dummy = $d; }
 * }
 *
 * // add an instance:
 * Container::getInstance()->addInstance(Foo::class, new Bar());
 *
 * // add a definition:
 * Container::getInstance()->addInstance(Foo::class, Bar::class);
 *
 * // retrieve an object:
 * $foo = Container::getInstance()->get(Foo::class);
 *
 * // $foo is an instance of Bar, and $dummy property of Bar is initialized and is an instance of Dummy.
 * </code>
 */
class Container
{
    /**
     * @var array
     */
    private $instances = array();

    /**
     * @var array
     */
    private $definitions = array();

    public function __construct()
    {
    }

    /**
     * Removes all defined instances and definitions.
     */
    public function clear()
    {
        $this->clearDefinitions();
        $this->clearInstances();
    }

    /**
     * Removes all defined instances.
     */
    public function clearInstances()
    {
        $this->instances = array();
    }

    /**
     * Removes all defined definitions.
     */
    public function clearDefinitions()
    {
        $this->definitions = array();
    }

    /**
     * Add a class definition
     *
     * @param string $key The name of interface or abstract class.
     * @param string $class The name of the implementation of the $key interface/abstract class.
     * @throws ClassDoesNotExistException
     */
    public function addDefinition(string $key, string $class)
    {
        if (class_exists($class) === false) {
            throw new ClassDoesNotExistException($class);
        }

        if (isset($this->definitions[$key]) === true) {
            trigger_error(sprintf("%s already exists in definitions", $key), E_USER_WARNING);
        }

        $this->definitions[$key] = $class;
    }

    /**
     * Add an instance of a object.
     *
     * @param string $key The name of interface or abstract class.
     * @param mixed $obj
     */
    public function addInstance(string $key, $obj)
    {
        if (isset($this->instances[$key]) === true) {
            trigger_error(sprintf("%s already exists in instances", $key), E_USER_WARNING);
        }

        $this->instances[$key] = $obj;
    }

    /**
     * Return an instance of the class associated to the <i>$key</i>.
     *
     * @param string $key The name of interface or abstract class.
     * @return object The requested object.
     */
    public function get($key)
    {
        if (isset($this->instances[$key]) == false) {
            if (isset($this->definitions[$key])) {
                $className = $this->definitions[$key];
            } else {
                trigger_error(sprintf("%s is not in container", $key), E_USER_NOTICE);

                $className = $key;
            }

            $this->instances[$key] = $this->instantiateClass($className);
        }

        return $this->instances[$key];
    }

    /**
     * This method does the magic.
     * It instantiates the class <i>$className</i> only if it implements {@link Injectable} interface.
     *
     * @param string $className The name of the class to instantiate.
     * @return object The requested object.
     * @throws NotInjectableException This exception will be throwed if the requested object does not implements the {@link Injectable} interface.
     * @throws ReflectionException Throwed if the class does not exist.
     */
    private function instantiateClass(string $className)
    {
        if (!in_array(Injectable::class, class_implements($className))) {
            throw new NotInjectableException($className);
        }

        $reflection = new \ReflectionClass($className);

        $paramsInstances = array();
        $constructor = $reflection->getConstructor();

        if (is_null($constructor) === false) {
            $params = $constructor->getParameters();
            foreach ($params AS $param) {
                $paramsInstances[] = $this->get($param->getClass()->name);
            }
        }

        return $reflection->newInstanceArgs($paramsInstances);
    }
}