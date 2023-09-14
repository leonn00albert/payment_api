<?php

namespace App\Application\Models;

use PDO;
use PDOException;

class User
{
    /**
     * Registers a user and inserts their information into the database.
     *
     * @param PDO $db The PDO database connection.
     * @param array $validatedData An array containing validated user data including 'email' and 'api_key'.
     *
     * @return bool Returns true on success, false on failure.
     */
    public static function register(PDO $db, array $validatedData): bool
    {
        $sth = $db->prepare("INSERT INTO users (email, api_key) 
        VALUES (:email, :api_key)");
        $sth->bindParam(':email', $validatedData['email']);
        $sth->bindParam(':api_key', $validatedData['api_key']);
        $result = $sth->execute();
        return $result;
    }
}
