<?php

namespace App\Utils\Errors;

/**
 * Class Errors
 *
 * This class provides error code handling and corresponding error messages for MySQL error codes.
 */
class Errors
{
    /**
     * Handle a MySQL error code and return an error message.
     *
     * @param int $code The MySQL error code to handle.
     *
     * @return string The corresponding error message for the provided error code.
     */
    public static function handleErrorCode(int $code)
    {
        return match ($code) {
            1062 => "Email already taken",
            1045 => "Access denied for user",
            1071 => "Key length too long",
            1146 => "Table does not exist",
            1215 => "Cannot add foreign key constraint",
            1216 => "Cannot add/update a child row: a foreign key constraint fails",
            1265 => "Data truncated for column",
            1364 => "Field does not have a default value",
            2002 => "Can't connect to the database server",
            1040 => "Too many connections",
            1064 => "syntax error",
            1452 => "Cannot add/update a child row: a foreign key constraint fails",
            default => "Unknown error",
        };
    }
}
