<?php

namespace App\Application\Controllers\Movie;

class MovieSanitizer
{
    /**
     * Sanitizes and validates user data for movie registration.
     *
     * @param array $data The user data to be sanitized and validated.
     *
     * @return array|false An array of sanitized and validated data on success, or false on failure.
     */
    public static function sanitize($data): array | false
    {
        $validatedData = [];
        if (isset($data['uid'])) {
            $validatedData['uid'] = filter_var($data['uid'], FILTER_SANITIZE_STRING);
        }
        if (isset($data['title']) && is_string($data['title'])) {
            $validatedData['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING);
        } else {
            return false;
        }
        if (isset($data['year'])) {
            $year = filter_var($data['year'], FILTER_VALIDATE_INT);
            if ($year !== false && $year >= 1900 && $year <= 2100) {
                $validatedData['year'] = $year;
            } else {
                return false;
            }
        }
        if (isset($data['released'])) {
            $validatedData['released'] = filter_var($data['released'], FILTER_SANITIZE_STRING);
        }

        if (isset($data['runtime'])) {
            $validatedData['runtime'] = filter_var($data['runtime'], FILTER_SANITIZE_STRING);
        }
        if (isset($data['genre'])) {
            $validatedData['genre'] = filter_var($data['genre'], FILTER_SANITIZE_STRING);
        }

        if (isset($data['director'])) {
            $validatedData['director'] = filter_var($data['director'], FILTER_SANITIZE_STRING);
        }


        if (isset($data['actors'])) {
            $validatedData['actors'] = filter_var($data['actors'], FILTER_SANITIZE_STRING);
        }


        if (isset($data['country'])) {
            $validatedData['country'] = filter_var($data['country'], FILTER_SANITIZE_STRING);
        }


        if (isset($data['poster'])) {
            $validatedData['poster'] = filter_var($data['poster'], FILTER_SANITIZE_STRING);
        }


        if (isset($data['imdb'])) {
            $validatedData['imdb'] = filter_var($data['imdb'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        }

        if (isset($data['type'])) {
            $validatedData['type'] = filter_var($data['type'], FILTER_SANITIZE_STRING);
        }


        if (isset($data['overview'])) {
            $validatedData['overview'] = filter_var($data['overview'], FILTER_SANITIZE_STRING);
        }

        if (isset($data['imdb_id'])) {
            $validatedData['imdb_id'] = filter_var($data['imdb_id'], FILTER_SANITIZE_STRING);
        }
        $requiredFields = ['uid', 'title'];
        foreach ($requiredFields as $field) {
            if (!isset($validatedData[$field])) {
                return false; // Missing required field
            }
        }

        return $validatedData;
    }
}
