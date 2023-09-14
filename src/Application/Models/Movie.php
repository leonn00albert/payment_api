<?php

namespace App\Application\Models;

use PDO;
use PDOException;
use App\Application\Models\MovieInterface;

class Movie implements MovieInterface
{
    protected string $table = 'movies';
    protected string $primaryKey = 'uid';

    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public static function all(PDO $db): array
    {
        $sth = $db->prepare("SELECT * FROM movies");
        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
    public static function findByUid(PDO $db, int $uid): ?array
    {
        $sth = $db->prepare("SELECT * FROM movies WHERE uid = :uid LIMIT 1");
        $sth->bindParam(':uid', $uid, PDO::PARAM_STR);
        $sth->execute();
        $data = $sth->fetch(PDO::FETCH_ASSOC);

        return $data ? $data : null;
    }

    public static function byNumberPerPage(PDO $db, int $numberPerPage): ?array
    {
            /** @var PDO $db */
            $sth = $db->prepare("SELECT * FROM movies LIMIT :offset, :n");
            $offset = 0;
            $sth->bindValue(':offset', $offset, PDO::PARAM_INT);
            $sth->bindValue(':n', $numberPerPage, PDO::PARAM_INT);
            $sth->execute();
            return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function byNumberPerPageAndSort(PDO $db, int $numberPerPage, string $sort): ?array
    {
        /** @var PDO $db */
        $offset = 0;
        $sth = $db->prepare("SELECT * FROM movies ORDER BY $sort LIMIT :offset, :n");
        $sth->bindParam(':offset', $offset, PDO::PARAM_INT);
        $sth->bindParam(':n', $numberPerPage, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function byNumberPerPageAndFilter(PDO $db, int $numberPerPage, string $filter): ?array
    {
        $offset = 0;
        $sql = "SELECT :f FROM movies LIMIT :offset, :n";
        $sth = $db->prepare($sql);
        $sth->bindParam(':f', $filter, PDO::PARAM_STR);
        $sth->bindParam(':offset', $offset, PDO::PARAM_INT);
        $sth->bindParam(':n', $numberPerPage, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function byNumberPerPageAndSearch(PDO $db, int $numberPerPage, string $search): ?array
    {
        $offset = 0;
        $sort = 'column_name_to_sort_by ASC';
        $sql = "SELECT * FROM movies WHERE title LIKE :search LIMIT :offset, :n";
        $sth = $db->prepare($sql);
        $sth->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $sth->bindParam(':offset', $offset, PDO::PARAM_INT);
        $sth->bindParam(':n', $numberPerPage, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function findById(PDO $db, int $id): array
    {
        $sth = $db->prepare("SELECT * FROM movies WHERE id = :id LIMIT 1");
        $sth->bindParam(':id', $id);
        $sth->execute();
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        return $data;
    }
    public static function create(PDO $db, array $validatedData): bool
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

    public static function updateById(PDO $db, int $id, array $validatedData): bool
    {
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
        return $sth->execute();
    }

    public static function deleteById(PDO $db, int $id): bool
    {
        try {
            $sth = $db->prepare("DELETE FROM movies WHERE uid = :id");
            $sth->bindParam(':id', $id, PDO::PARAM_INT);
            $sth->execute();
            return $sth->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
