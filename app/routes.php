<?php

declare(strict_types=1);

use App\Application\Controllers\Movie\MovieConroller;
use App\Utils\SeedMovies;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    
    $app->get('/v1/movies', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM movies");
        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/seed', function (Request $request, Response $response) {
        $seeder = new SeedMovies();
        $seed_data = $seeder->seed();        $db = $this->get(PDO::class);
        $db = $this->get(PDO::class);

        foreach ($seed_data as $movie) {
            $sql = "INSERT INTO movies (uid, title, year, released, runtime, overview, genre, director, actors, country, poster, imdb_id, imdb, type) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $movie->uid,
                $movie->title,
                $movie->year,
                $movie->released,
                $movie->runtime,
                $movie->overview,
                $movie->genre,
                $movie->director,
                $movie->actors,
                $movie->country,
                $movie->poster,
                $movie->imdb_id,
                $movie->imdb,
                $movie->type
            ]);
        }
;

        
        return $response->withHeader('Content-Type', 'application/json');
    });
};
