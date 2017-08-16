<?php

namespace mpstyle\container;

use Closure;
use ReflectionFunction;

/**
 * Lazy and naive container for the dependency injection.<br>
 * Use the Flyweight design pattern to store a single instance of injectable classes.<br>
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
     * @var array
     */
    private $injectableBehavior = [];

    public function __construct()
    {
    }

    /**
     * Removes all defined instances and definitions.
     */
    public function clear()
    {
        $this->injectableBehavior = [];
    }

    /**
     * Add a class definition
     *
     * @param string $key The name of interface or abstract class.
     * @param string $class The name of the implementation of the $key interface/abstract class.
     * @throws NotInjectableException
     */
    public function addDefinition( string $key, string $class )
    {
        if( class_exists( $class ) == false || !array_intersect( [Injectable::class, $key], class_implements( $class ) ) )
        {
            throw new NotInjectableException( $key );
        }

        $this->injectableBehavior[$key] = function () use ( $class )
        {
            return $this->getInstanceByClass( $class );
        };
    }

    /**
     * Add an instance of a object.
     *
     * @param string $key The name of interface or abstract class.
     * @param mixed $obj
     * @throws NotInjectableException
     */
    public function addInstance( string $key, $obj )
    {
        if( !array_intersect( [Injectable::class, $key], class_implements( $obj ) ) )
        {
            throw new NotInjectableException( $key );
        }

        $this->injectableBehavior[$key] = function () use ( $obj )
        {
            return $obj;
        };
    }

    /**
     * Add a {@link Closure} to the container.
     *
     * @param string $key
     * @param Closure $closure
     * @throws NotInjectableException
     */
    public function addClosure( string $key, Closure $closure )
    {
        $reflection = new ReflectionFunction( $closure );
        $returnType = $reflection->getReturnType();
        if( is_null( $returnType ) === false && !array_intersect( [Injectable::class, $key], [(string)$returnType] ) )
        {
            throw new NotInjectableException( $key );
        }

        $this->injectableBehavior[$key] = function () use ( $closure )
        {
            return $this->getInstanceByClosure( $closure );
        };
    }

    /**
     * Return an instance of the class associated to the <i>$key</i>.
     *
     * @deprecated Use {@link Container#getInstance} instead.
     * @param string <i>$key</i> The name of interface or abstract class.
     * @return object The requested object.
     */
    public function get( $key )
    {
        return $this->getInstance( $key );
    }

    /**
     * Return an instance of the class associated to the <i>$key</i>.
     *
     * @param string <i>$key</i> The name of interface or abstract class.
     * @return object The requested object.
     */
    public function getInstance( $key )
    {
        if( $this->existsKey( $key ) === false )
        {
            $this->addDefinition( $key, $key );
        }

        $instance = $this->injectableBehavior[$key]();
        $this->addInstance( $key, $instance );

        return $instance;
    }

    public function existsKey( $key )
    {
        return isset($this->injectableBehavior[$key]);
    }

    /**
     * Returns an instance of {@link Container} adding all the definitions in the INI file <i>$path</i>
     *
     * @param string $path
     * @return Container
     */
    public static function fromIni( string $path ): Container
    {
        $container = new Container();
        $rows = parse_ini_file( $path );

        foreach( $rows as $key => $class )
        {
            $container->addDefinition( $key, $class );
        }

        return $container;
    }

    /**
     * Returns an instance of {@link Container} adding all the definitions in the PHP file <i>$path</i>
     *
     * @param string $path
     * @return Container
     */
    public static function fromPHP( string $path ): Container
    {
        $container = new Container();
        $rows = include($path);

        foreach( $rows as $key => $value )
        {
            switch( true )
            {
                case (is_string( $value )):
                    $container->addDefinition( $key, $value );
                    break;
                case (is_callable( $value )):
                    $container->addClosure( $key, $value );
                    break;
                default:
                    $container->addInstance( $key, $value );
                    break;
            }
        }

        return $container;
    }

    private function getInstanceByClosure( Closure $closure )
    {
        $paramsInstances = [];
        $reflection = new ReflectionFunction( $closure );
        $params = $reflection->getParameters();

        foreach( $params AS $param )
        {
            $paramsInstances[] = $this->getInstance( $param->getClass()->name );
        }

        $instance = $reflection->invokeArgs( $paramsInstances );

        return $instance;
    }

    private function getInstanceByClass( string $class )
    {
        $reflection = new \ReflectionClass( $class );

        $paramsInstances = [];
        $constructor = $reflection->getConstructor();

        if( is_null( $constructor ) === false )
        {
            $params = $constructor->getParameters();

            foreach( $params AS $param )
            {
                $paramsInstances[] = $this->getInstance( $param->getClass()->name );
            }
        }

        $instance = $reflection->newInstanceArgs( $paramsInstances );

        return $instance;
    }
}

