<?php

namespace App\Application\Models;

use PDO;
use PDOException;

class User
{
    public static function register(PDO $db, array $validatedData)
    {
        $sth = $db->prepare("INSERT INTO users (email, api_key) 
        VALUES (:email, :api_key)");
        $sth->bindParam(':email', $validatedData['email']);
        $sth->bindParam(':api_key', $validatedData['api_key']);
        $result = $sth->execute();
        return $result;
    }
}
