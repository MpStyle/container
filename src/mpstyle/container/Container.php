<?php

namespace mpstyle\container;

use Closure;

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

    /**
     * @var InstanceFactory
     */
    private $instance;

    public function __construct()
    {
        $this->instance = new InstanceFactory();
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
*@param string $key The name of interface or abstract class.
     * @param string $class The name of the implementation of the $key interface/abstract class.
     * @throws NotInjectableException
     */
    public function addDefinition( string $key, string $class )
    {
        if( !array_intersect( [Injectable::class, $key], class_implements( $class ) ) )
        {
            throw new NotInjectableException( $class );
        }

        $this->injectableObjects[$key] = new InjectableObject( InjectableObjectType::CLASS_TYPE, $class );
    }

    /**
     * Add an instance of a object.

     *
*@param string $key The name of interface or abstract class.
     * @param mixed $obj
     * @throws NotInjectableException
     */
    public function addInstance( string $key, $obj )
    {
        if( !array_intersect( [Injectable::class, $key], class_implements( $obj ) ) )
        {
            throw new NotInjectableException( $key );
        }

        $this->injectableObjects[$key] = new InjectableObject( InjectableObjectType::OBJECT_TYPE, $obj );
    }

    /**
     * Add a {@link Closure} to the container.
     *
     * @param string $key
     * @param Closure $obj
     */
    public function addClosure( string $key, Closure $obj )
    {
        $this->injectableObjects[$key] = new InjectableObject( InjectableObjectType::CLOSURE_TYPE, $obj );
    }

    /**
     * Return an instance of the class associated to the <i>$key</i>.
     *
     * @param string $key The name of interface or abstract class.
     * @return object The requested object.
     */
    public function get( $key )
    {
        if( !isset($this->injectableObjects[$key]) )
        {
            $this->addDefinition( $key, $key );
            $instance = $this->get( $key );
        }
        else
        {
            $instance = $this->instance->getInjectableObject( $this->injectableObjects[$key] );
        }

        $this->addInstance( $key, $instance );

        return $this->injectableObjects[$key]->getValue();
    }
}

