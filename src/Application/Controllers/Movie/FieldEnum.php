<?php
declare(strict_types=1);

namespace App\Application\Controllers\Movie;
/**
 * Enum representing fields for a movie.
 */
enum FieldEnum: string
{
    case uid = 'uid';
    case title = 'title';
    case year = 'year';
    case released = 'released';
    case runtime = 'runtime';
    case genre = 'genre';
    case director = 'director';
    case actors = 'actors';
    case country = 'country';
    case poster = 'poster';
    case imdb = 'imdb';
    case type = 'type';
    case created_at = 'created_at';
    case updated_at = 'updated_at';
    case overview = 'overview';
    case imdb_id = 'imdb_id';
    /**
     * Check if a given value is a valid field.
     *
     * @param string $value The value to check.
     * @return bool True if the value is a valid field, false otherwise.
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, Field::toArray(), false);
    }
    /**
     * Get an array of all valid field values.
     *
     * @return array An array of valid field values.
     */
    public static function toArray(): array
    {
        return  array_column(Field::cases(), 'value');
    }
}