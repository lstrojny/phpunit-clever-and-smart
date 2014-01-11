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

    /**
     * @param object $object
     * @param string $propertyName
     * @return mixed
     */
    public static function getInvisibleProperty($object, $propertyName)
    {
        return static::getPropertyReflection($object, $propertyName)->getValue($object);
    }

    /**
     * @param object $object
     * @param string $propertyName
     * @param mixed $value
     */
    public static function setInvisibleProperty($object, $propertyName, $value)
    {
        static::getPropertyReflection($object, $propertyName)->setValue($object, $value);
    }

    /**
     * @param object $object
     * @param string $propertyName
     * @return ReflectionProperty
     */
    private static function getPropertyReflection($object, $propertyName)
    {
        $reflected = new ReflectionObject($object);

        $property = $reflected->getProperty($propertyName);
        $property->setAccessible(true);

        return $property;
    }
}
