<?php

namespace mpstyle\container;

/**
 * Class UniqueContainer wrap a singleton instance of a {@link Container}
 */
class UniqueContainer
{
    /**
     * @var Container
     */
    private static $container = null;

    /**
     * UniqueContainer constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return Container
     */
    public static function get()
    {
        if (self::$container == null) {
            self::$container = new Container();
        }

        return self::$container;
    }
}