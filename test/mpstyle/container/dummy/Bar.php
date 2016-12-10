<?php


namespace mpstyle\container\dummy;


class Bar implements Foo
{
    public $dummy;

    public function __construct( Dummy $d )
    {
        $this->dummy = $d;
    }
}