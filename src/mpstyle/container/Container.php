<?php

namespace mpstyle\container;

use Closure;
use ReflectionFunction;

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
 *     public function __construct(Dummy $d){ $this->dummy = $d; }
 * }
 * // add an instance:
 * Container::getInstance()->addInstance(Foo::class, new Bar());
 * // add a definition:
 * Container::getInstance()->addInstance(Foo::class, Bar::class);
 * // retrieve an object:
 * $foo = Container::getInstance()->get(Foo::class);
 * // $foo is an instance of Bar, and $dummy property of Bar is initialized and is an instance of Dummy.
 * </code>
 */
class Container
{
    /**
     * @var InjectableObject[]
     */
    private $injectableObjects = [];

    private $behavior = [];

    public function __construct()
    {
        $this->behavior[InjectionType::CLASS_TYPE] = function (string $injectableObjectValue) {
            return $this->getInstanceByClass($injectableObjectValue);
        };

        $this->behavior[InjectionType::CLOSURE_TYPE] = function (Closure $injectableObjectValue) {
            return $this->getInstanceByClosure($injectableObjectValue);
        };

        $this->behavior[InjectionType::OBJECT_TYPE] = function ($injectableObjectValue) {
            return $injectableObjectValue;
        };
    }

    /**
     * Removes all defined instances and definitions.
     */
    public function clear()
    {
        $this->injectableObjects = [];
    }

    /**
     * Add a class definition
     *
     * @param string $key The name of interface or abstract class.
     * @param string $class The name of the implementation of the $key interface/abstract class.
     * @throws NotInjectableException
     */
    public function addDefinition(string $key, string $class)
    {
        if (!array_intersect([Injectable::class, $key], class_implements($class))) {
            throw new NotInjectableException($key);
        }

        $this->injectableObjects[$key] = new InjectableObject(InjectionType::CLASS_TYPE, $class);
    }

    /**
     * Add an instance of a object.
     *
     * @param string $key The name of interface or abstract class.
     * @param mixed $obj
     * @throws NotInjectableException
     */
    public function addInstance(string $key, $obj)
    {
        if (!array_intersect([Injectable::class, $key], class_implements($obj))) {
            throw new NotInjectableException($key);
        }

        $this->injectableObjects[$key] = new InjectableObject(InjectionType::OBJECT_TYPE, $obj);
    }

    /**
     * Add a {@link Closure} to the container.
     *
     * @param string $key
     * @param Closure $closure
     * @throws NotInjectableException
     */
    public function addClosure(string $key, Closure $closure)
    {
        $reflection = new ReflectionFunction($closure);
        $returnType = $reflection->getReturnType();
        if (is_null($returnType) === false && !array_intersect([Injectable::class, $key], [(string)$returnType])) {
            throw new NotInjectableException($key);
        }

        $this->injectableObjects[$key] = new InjectableObject(InjectionType::CLOSURE_TYPE, $closure);
    }

    /**
     * Return an instance of the class associated to the <i>$key</i>.
     *
     * @deprecated Use {@link Container#getInstance} instead.
     * @param string <i>$key</i> The name of interface or abstract class.
     * @return object The requested object.
     */
    public function get($key)
    {
        return $this->getInstance($key);
    }

    /**
     * Return an instance of the class associated to the <i>$key</i>.
     *
     * @param string <i>$key</i> The name of interface or abstract class.
     * @return object The requested object.
     */
    public function getInstance($key)
    {
        if (isset($this->injectableObjects[$key]) === false) {
            $this->addDefinition($key, $key);
        }

        $type = $this->injectableObjects[$key]->getType();
        $value = $this->injectableObjects[$key]->getValue();
        $instance = $this->behavior[$type]($value);

        $this->injectableObjects[$key] = new InjectableObject(InjectionType::OBJECT_TYPE, $instance);

        return $instance;
    }

    private function getInstanceByClosure(Closure $closure)
    {
        $paramsInstances = [];
        $reflection = new ReflectionFunction($closure);
        $params = $reflection->getParameters();

        foreach ($params AS $param) {
            $paramsInstances[] = $this->getInstance($param->getClass()->name);
        }

        $instance = $reflection->invokeArgs($paramsInstances);

        return $instance;
    }

    private function getInstanceByClass(string $class)
    {
        $reflection = new \ReflectionClass($class);

        $paramsInstances = [];
        $constructor = $reflection->getConstructor();

        if (is_null($constructor) === false) {
            $params = $constructor->getParameters();

            foreach ($params AS $param) {
                $paramsInstances[] = $this->getInstance($param->getClass()->name);
            }
        }

        $instance = $reflection->newInstanceArgs($paramsInstances);

        return $instance;
    }
}

