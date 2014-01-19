<?php
namespace PHPUnit\Runner\CleverAndSmart;

use PHPUnit\Runner\CleverAndSmart\Exception\PropertyReflectionException;
use ReflectionException;
use ReflectionObject;
use ReflectionProperty;

final class Util
{
    /**
     * Create unique run ID
     *
     * @return string
     */
    public static function createRunId()
    {
        $random = uniqid(true) . mt_rand();

        if (function_exists('openssl_random_pseudo_bytes')) {
            $random .= openssl_random_pseudo_bytes(1024);
        }

        return hash('sha512', $random . microtime(true) . getmypid() . get_current_user());
    }

    /**
     * Get an invisible property from an object (private or protected)
     *
     * @param object $object
     * @param string $propertyName
     * @param string $methodName
     * @return mixed
     */
    public static function getInvisibleProperty($object, $propertyName, $methodName = null)
    {
        if (method_exists($object, $methodName)) {
            return $object->{$methodName}();
        }

        return static::getPropertyReflection($object, $propertyName)->getValue($object);
    }

    /**
     * Set an invisible property from an object (private or protected)
     *
     * @param object $object
     * @param string $propertyName
     * @param mixed $value
     * @param string $methodName
     */
    public static function setInvisibleProperty($object, $propertyName, $value, $methodName = null)
    {
        if (method_exists($object, $methodName)) {
            $object->{$methodName}($value);
            return;
        }

        static::getPropertyReflection($object, $propertyName)->setValue($object, $value);
    }

    /**
     * Get proper PropertyReflection object
     *
     * @param object $object
     * @param string $propertyName
     * @throws ReflectionException
     * @return ReflectionProperty
     */
    private static function getPropertyReflection($object, $propertyName)
    {
        $reflected = new ReflectionObject($object);

        $classHierarchy = array();

        do {
            try {

                $property = $reflected->getProperty($propertyName);
                $property->setAccessible(true);

                return $property;

            } catch (ReflectionException $e) {

                $classHierarchy[] = $reflected->getName();
                $e = PropertyReflectionException::propertyNotExistsInHierarchy($propertyName, $e, $classHierarchy);

            }
        } while ($reflected = $reflected->getParentClass());

        throw $e;
    }
}
