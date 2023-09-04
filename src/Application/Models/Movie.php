<?php
namespace App\Application\Models;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use PDO;
use App\Application\Models\MovieInterface;
class Movie implements MovieInterface
{
    protected $table = 'movies';
    protected $primaryKey = 'uid';

    protected $db; // Store the database connection

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public static function all(PDO $db):array
    {
        $sth = $db->prepare("SELECT * FROM movies");
        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    } 
}
