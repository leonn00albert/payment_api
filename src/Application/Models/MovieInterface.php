<?php
namespace App\Application\Models;
use PDO;

interface MovieInterface {
    public static function all(PDO $db):array;
}