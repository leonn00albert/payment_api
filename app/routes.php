<?php

declare(strict_types=1);

use App\Application\Controllers\Auth\AuthController;
use App\Application\Controllers\Docs\DocsController;
use App\Application\Controllers\Movie\MovieController;
use App\Utils\SeedMovies;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use OpenApi\Annotations as OA;
use Psr\Log\LoggerInterface;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $movieController = new MovieController($app->getContainer()->get(PDO::class), $app->getContainer()->get(LoggerInterface::class));
    $docsController = new DocsController;
    $AuthController = new AuthController;

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });


    $app->get('/swagger.json', $docsController->swaggerFile());
    $app->post('/register', $AuthController->register());
    $app->get('/docs', $docsController->index());
    $app->get('/v1/movies', $movieController->index());
    $app->get('/v1/movie/{uid}', $movieController->read());
    $app->post('/v1/movies', $movieController->create());
    $app->put('/v1/movies/{id}', $movieController->update());
    $app->patch('/v1/movies/{id}', $movieController->patch());
    $app->delete('/v1/movies/{id}', $movieController->delete());
    $app->get('/v1/movies/{numberPerPage}', $movieController->moviesPerPage());
    $app->get('/v1/movies/{numberPerPage}/sort/{sort}', $movieController->moviesPerPageAndSort());
    $app->get('/v1/movies/{numberPerPage}/filter/{filter}', $movieController->moviesPerPageAndFilter());
    $app->get('/v1/movies/{numberPerPage}/search/{search}', $movieController->moviesPerPageAndSearch());

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
