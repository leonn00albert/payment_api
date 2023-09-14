<?php

namespace App\Application\Models;

use PDO;

/**
 * Interface MovieInterface
 * Defines methods for interacting with movie data.
 */
interface MovieInterface
{
    /**
     * Get all movies from the database.
     *
     * @param PDO $db The database connection.
     * @return array An array of movie records.
     */
    public static function all(PDO $db): array;

    /**
     * Find movies by UID.
     *
     * @param PDO $db The database connection.
     * @param int $uid The UID of the movie to find.
     * @return array An array of movie records matching the UID.
     */
    public static function findByUid(PDO $db, int $uid): ?array;

    /**
     * Find a movie by its ID.
     *
     * @param PDO $db The database connection.
     * @param int $id The ID of the movie to find.
     * @return array|null The movie record, or null if not found.
     */
    public static function findById(PDO $db, int $id): ?array;

    /**
     * Create a new movie record in the database.
     *
     * @param PDO $db The database connection.
     * @param array $validatedData The validated movie data to create.
     * @return bool True if the movie was created successfully, false otherwise.
     */
    public static function create(PDO $db, array $validatedData): bool;

    /**
     * Update a movie record by its ID.
     *
     * @param PDO $db The database connection.
     * @param int $id The ID of the movie to update.
     * @param array $validatedData The validated movie data to update.
     * @return bool True if the movie was updated successfully, false otherwise.
     */
    public static function updateById(PDO $db, int $id, array $validatedData): bool;

    /**
     * Delete a movie record by its ID.
     *
     * @param PDO $db The database connection.
     * @param int $id The ID of the movie to delete.
     * @return bool True if the movie was deleted successfully, false otherwise.
     */
    public static function deleteById(PDO $db, int $id): bool;
}
