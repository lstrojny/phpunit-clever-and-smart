<?php
namespace PHPUnit\Runner\CleverAndSmart\Exception;

use ReflectionException as BaseReflectionException;

class PropertyReflectionException extends BaseReflectionException
{
    public static function propertyNotExistsInHierarchy(
        $propertyName,
        BaseReflectionException $exception,
        array $classHierarchy
    )
    {
        return new static(
            sprintf('Property "%s" does not exist in hierarchy %s', $propertyName, implode(' < ', $classHierarchy)),
            null,
            $exception
        );
    }
}
