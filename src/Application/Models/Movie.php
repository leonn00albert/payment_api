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
    public static function findByUid(PDO $db, int $id):array
    {
        $sth = $db->prepare("SELECT * FROM movies WHERE uid = :id");

        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    } 
    public static function findById(PDO $db, int $id):array
    {
        $sth = $db->prepare("SELECT * FROM movies WHERE id = :id LIMIT 1");
        $sth->execute();
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        return $data;
    } 
    public static function create(PDO $db, array $validatedData):bool
    {
            $sth = $db->prepare("INSERT INTO movies (uid, title, year, released, runtime, genre, director, actors, country, poster, imdb, type, created_at, updated_at, overview, imdb_id) 
                                 VALUES (:uid, :title, :year, :released, :runtime, :genre, :director, :actors, :country, :poster, :imdb, :type, NOW(), NOW(), :overview, :imdb_id)");
            $sth->bindParam(':uid', $validatedData['uid']);
            $sth->bindParam(':title', $validatedData['title']);
            $sth->bindParam(':year', $validatedData['year']);
            $sth->bindParam(':released', $validatedData['released']);
            $sth->bindParam(':runtime', $validatedData['runtime']);
            $sth->bindParam(':genre', $validatedData['genre']);
            $sth->bindParam(':director', $validatedData['director']);
            $sth->bindParam(':actors', $validatedData['actors']);
            $sth->bindParam(':country', $validatedData['country']);
            $sth->bindParam(':poster', $validatedData['poster']);
            $sth->bindParam(':imdb', $validatedData['imdb']);
            $sth->bindParam(':type', $validatedData['type']);
            $sth->bindParam(':overview', $validatedData['overview']);
            $sth->bindParam(':imdb_id', $validatedData['imdb_id']);
    
            $result = $sth->execute();
            return $result;
    } 

    public static function updateById(PDO $db, int $id, array $validatedData):bool{
        $id = $args['id'];
        $db = $this->get(PDO::class);
        $sth = $db->prepare("UPDATE movies 
                             SET uid = :uid, title = :title, year = :year, released = :released, 
                                 runtime = :runtime, genre = :genre, director = :director, 
                                 actors = :actors, country = :country, poster = :poster, 
                                 imdb = :imdb, type = :type, updated_at = NOW(), overview = :overview, 
                                 imdb_id = :imdb_id
                             WHERE id = :id");

        $sth->bindParam(':id', $id);
        $sth->bindParam(':uid', $validatedData['uid']);
        $sth->bindParam(':title', $validatedData['title']);
        $sth->bindParam(':year', $validatedData['year']);
        $sth->bindParam(':released', $validatedData['released']);
        $sth->bindParam(':runtime', $validatedData['runtime']);
        $sth->bindParam(':genre', $validatedData['genre']);
        $sth->bindParam(':director', $validatedData['director']);
        $sth->bindParam(':actors', $validatedData['actors']);
        $sth->bindParam(':country', $validatedData['country']);
        $sth->bindParam(':poster', $validatedData['poster']);
        $sth->bindParam(':imdb', $validatedData['imdb']);
        $sth->bindParam(':type', $validatedData['type']);
        $sth->bindParam(':overview', $validatedData['overview']);
        $sth->bindParam(':imdb_id', $validatedData['imdb_id']);

        $result = $sth->execute();

        if ($result) {
            $response->getBody()->write(json_encode(['message' => 'Movie updated successfully']));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(['message' => 'Failed to update movie']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
