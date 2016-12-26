<?php

namespace mpstyle\container;

/**
 */
class InjectableObject
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string|callable|object
     */
    private $value;

    /**
     * InjectableObject constructor.
     *
     * @param string $type
     * @param callable|object|string $value
     */
    public function __construct( string $type, $value )
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return InjectableObject
     */
    public function setType( string $type ): InjectableObject
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return callable|object|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param callable|object|string $value
     * @return InjectableObject
     */
    public function setValue( $value )
    {
        $this->value = $value;

        return $this;
    }


}