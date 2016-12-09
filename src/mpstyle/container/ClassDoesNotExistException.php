<?php

namespace mpstyle\container;


class ClassDoesNotExistException extends \Exception
{
    public function __construct(string $className)
    {
        parent::__construct(sprintf("Class %s does not exist", $className));
    }
}