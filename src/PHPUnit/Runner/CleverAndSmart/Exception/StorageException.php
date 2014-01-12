<?php
namespace PHPUnit\Runner\CleverAndSmart\Exception;

class StorageException extends RuntimeException
{
    public static function databaseError($errorMessage, $errorCode)
    {
        return new static(sprintf('%s (error code %s)', $errorMessage, $errorCode));
    }
}
