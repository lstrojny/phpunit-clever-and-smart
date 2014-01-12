<?php
namespace PHPUnit\Runner\CleverAndSmart\Exception;

use ReflectionException as BaseReflectionException;

class PropertyReflectionException extends BaseReflectionException
{
    public static function propertyNotExistsInHierarchy(BaseReflectionException $exception, array $classHierarchy)
    {
        return new static(
            $exception->getMessage() . ' in hierarchy ' . implode(' < ', $classHierarchy),
            null,
            $exception
        );
    }
}
