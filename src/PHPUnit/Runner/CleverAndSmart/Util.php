<?php
namespace PHPUnit\Runner\CleverAndSmart;

use ReflectionObject;
use ReflectionProperty;

final class Util
{
    /**
     * @return string
     */
    public static function getRunId()
    {
        return hash('sha512', microtime(true) . getmypid() . get_current_user() . srand());
    }
}
