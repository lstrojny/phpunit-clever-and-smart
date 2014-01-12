<?php
namespace PHPUnit\Runner\CleverAndSmart;

use ReflectionException;
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
     * @param string $methodName
     * @param string $propertyName
     * @return mixed
     */
    public static function getInvisibleProperty($object, $methodName, $propertyName)
    {
        if (method_exists($object, $methodName)) {
            return $object->{$methodName}();
        }

        return static::getPropertyReflection($object, $propertyName)->getValue($object);
    }

    /**
     * @param object $object
     * @param string $methodName
     * @param string $propertyName
     * @param mixed $value
     */
    public static function setInvisibleProperty($object, $methodName, $propertyName, $value)
    {
        if (method_exists($object, $methodName)) {
            $object->{$methodName}($value);
            return;
        }

        static::getPropertyReflection($object, $propertyName)->setValue($object, $value);
    }

    /**
     * @param object $object
     * @param string $propertyName
     * @throws ReflectionException
     * @return ReflectionProperty
     */
    private static function getPropertyReflection($object, $propertyName)
    {
        $reflected = new ReflectionObject($object);

        $classes = [];

        do {
            try {

                $property = $reflected->getProperty($propertyName);
                $property->setAccessible(true);

                return $property;

            } catch (ReflectionException $e) {

                $classes[] = $reflected->getName();
                $e = new ReflectionException($e->getMessage() . ' in ' . join(', ', $classes), null, $e);

            }
        } while ($reflected = $reflected->getParentClass());

        throw $e;
    }

}
