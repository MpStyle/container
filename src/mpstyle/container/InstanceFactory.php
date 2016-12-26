<?php

namespace mpstyle\container;

use ReflectionFunction;

class InstanceFactory
{
    private $behavior = [];

    public function __construct()
    {
        $this->behavior[InjectableObjectType::CLASS_TYPE] = function ( $injectableObjectValue )
        {
            $reflection = new \ReflectionClass( $injectableObjectValue );

            $paramsInstances = [];
            $constructor = $reflection->getConstructor();

            if( is_null( $constructor ) === false )
            {
                $params = $constructor->getParameters();

                foreach( $params AS $param )
                {
                    $paramsInstances[] = $this->getInjectableObject(
                        new InjectableObject(
                            InjectableObjectType::CLASS_TYPE,
                            $param->getClass()->name
                        )
                    );
                }
            }

            $instance = $reflection->newInstanceArgs( $paramsInstances );

            return $instance;
        };

        $this->behavior[InjectableObjectType::CLOSURE_TYPE] = function ( $injectableObjectValue )
        {
            $closure = $injectableObjectValue;

            $paramsInstances = [];
            $reflection = new ReflectionFunction( $closure );
            $params = $reflection->getParameters();

            foreach( $params AS $param )
            {
                $paramsInstances[] = $this->getInjectableObject(
                    new InjectableObject(
                        InjectableObjectType::CLASS_TYPE,
                        $param->getClass()->name
                    )
                );
            }

            $instance = $reflection->invokeArgs( $paramsInstances );

            return $instance;
        };

        $this->behavior[InjectableObjectType::OBJECT_TYPE] = function ( $injectableObjectValue )
        {
            return $injectableObjectValue;
        };
    }

    /**
     * @param InjectableObject $injectableObject
     * @return mixed
     */
    public function getInjectableObject( InjectableObject $injectableObject )
    {
        return $this->behavior[$injectableObject->getType()]( $injectableObject->getValue() );
    }
}