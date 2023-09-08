<?php

declare(strict_types=1);

use App\Application\Controllers\Docs\DocsController;
use App\Application\Controllers\Movie\MovieController;
use App\Utils\SeedMovies;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use OpenApi\Annotations as OA;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

/**
 * @OA\Info(
 *     title="My First API",
 *     version="0.1"
 * )
 */

$movieController = new MovieController;
$docsController = new DocsController;
return function (App $app) use ($movieController, $docsController){
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    /**
     * @OA\Get(
     *     path="/api/resource.json",
     *     @OA\Response(response="200", description="An example resource")
     * )
     */
    $app->get('/v1/swagger.json', $docsController->swaggerFile());
    $app->get('/v1/docs', $docsController->index());
    $app->get('/v1/movies', $movieController->index());
    $app->get('/v1/movie/{uid}', $movieController->read());
    $app->post('/v1/movies',$movieController->create());
    $app->put('/v1/movies/{id}',$movieController->update());
    $app->patch('/v1/movies/{id}',$movieController->patch());
    $app->delete('/v1/movies/{id}',$movieController->delete());

    $app->get('/v1/movies/{numberPerPage}', function (Request $request, Response $response, $args) {
        $numberPerPage = $args['numberPerPage'];

    });

    $app->get('/v1/movies/{numberPerPage}/sort/{fieldToSort}', function (Request $request, Response $response, $args) {
        $numberPerPage = $args['numberPerPage'];
        $fieldToSort = $args['fieldToSort'];

    });
    $app->get('/seed', function (Request $request, Response $response) {
        $seeder = new SeedMovies();
        $seed_data = $seeder->seed();
        $db = $this->get(PDO::class);
        $db = $this->get(PDO::class);

        foreach ($seed_data as $movie) {
            $sql = "INSERT INTO movies (uid, title, year, released, runtime, overview, genre, director, actors, country, poster, imdb_id, imdb, type) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $db->prepare($sql);
            $stmt->execute([]);
        };


        return $response->withHeader('Content-Type', 'application/json');
    });
};

function validateAndSanitizeMovieData($data)
{
    $validatedData = [];

    // Validate and sanitize 'uid' (assuming it's a string)
    if (isset($data['uid'])) {
        $validatedData['uid'] = filter_var($data['uid'], FILTER_SANITIZE_STRING);
    }

    // Validate and sanitize 'title' (assuming it's a required string)
    if (isset($data['title']) && is_string($data['title'])) {
        $validatedData['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING);
    } else {
        return false; // Invalid title
    }

    // Validate and sanitize 'year' (assuming it's an integer)
    if (isset($data['year'])) {
        $year = filter_var($data['year'], FILTER_VALIDATE_INT);
        if ($year !== false && $year >= 1900 && $year <= 2100) {
            $validatedData['year'] = $year;
        } else {
            return false; // Invalid year
        }
    }

    // Validate and sanitize 'released' (assuming it's a date in YYYY-MM-DD format)
    if (isset($data['released'])) {
        // You can add more specific date validation if needed
        $validatedData['released'] = filter_var($data['released'], FILTER_SANITIZE_STRING);
    }

    // Validate and sanitize 'runtime' (assuming it's a string)
    if (isset($data['runtime'])) {
        $validatedData['runtime'] = filter_var($data['runtime'], FILTER_SANITIZE_STRING);
    }

    // Validate and sanitize 'genre' (assuming it's a string)
    if (isset($data['genre'])) {
        $validatedData['genre'] = filter_var($data['genre'], FILTER_SANITIZE_STRING);
    }

    // Validate and sanitize 'director' (assuming it's a string)
    if (isset($data['director'])) {
        $validatedData['director'] = filter_var($data['director'], FILTER_SANITIZE_STRING);
    }

    // Validate and sanitize 'actors' (assuming it's a string)
    if (isset($data['actors'])) {
        $validatedData['actors'] = filter_var($data['actors'], FILTER_SANITIZE_STRING);
    }

    // Validate and sanitize 'country' (assuming it's a string)
    if (isset($data['country'])) {
        $validatedData['country'] = filter_var($data['country'], FILTER_SANITIZE_STRING);
    }

    // Validate and sanitize 'poster' (assuming it's a string)
    if (isset($data['poster'])) {
        $validatedData['poster'] = filter_var($data['poster'], FILTER_SANITIZE_STRING);
    }

    // Validate and sanitize 'imdb' (assuming it's a string or float)
    if (isset($data['imdb'])) {
        $validatedData['imdb'] = filter_var($data['imdb'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    // Validate and sanitize 'type' (assuming it's a string)
    if (isset($data['type'])) {
        $validatedData['type'] = filter_var($data['type'], FILTER_SANITIZE_STRING);
    }

    // Validate and sanitize 'overview' (assuming it's a string)
    if (isset($data['overview'])) {
        $validatedData['overview'] = filter_var($data['overview'], FILTER_SANITIZE_STRING);
    }

    // Validate and sanitize 'imdb_id' (assuming it's a string)
    if (isset($data['imdb_id'])) {
        $validatedData['imdb_id'] = filter_var($data['imdb_id'], FILTER_SANITIZE_STRING);
    }

    // Add more fields as needed

    // Check if all required fields are present
    $requiredFields = ['uid', 'title', 'year', 'released', 'runtime', 'director', 'actors', 'country', 'poster', 'imdb', 'type', 'overview', 'imdb_id'];

    foreach ($requiredFields as $field) {
        if (!isset($validatedData[$field])) {
            return false; // Missing required field
        }
    }

    return $validatedData;
}

function getMovieById($db, $id)
{
    $sth = $db->prepare("SELECT * FROM movies WHERE id = :id");
    $sth->bindParam(':id', $id);
    $sth->execute();
    return $sth->fetch(PDO::FETCH_ASSOC);
}
