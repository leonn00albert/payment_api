<?php

declare(strict_types=1);

namespace App\Application\Controllers\Movie;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Models\Movie;
use OpenApi\Annotations as OA;
use Exception;
use PDO;
use Psr\Log\LoggerInterface;

/**
 * Enum representing fields for a movie.
 */
enum Field: string
{
    case uid = 'uid';
    case title = 'title';
    case year = 'year';
    case released = 'released';
    case runtime = 'runtime';
    case genre = 'genre';
    case director = 'director';
    case actors = 'actors';
    case country = 'country';
    case poster = 'poster';
    case imdb = 'imdb';
    case type = 'type';
    case created_at = 'created_at';
    case updated_at = 'updated_at';
    case overview = 'overview';
    case imdb_id = 'imdb_id';
    /**
     * Check if a given value is a valid field.
     *
     * @param string $value The value to check.
     * @return bool True if the value is a valid field, false otherwise.
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, Field::toArray(), false);
    }
    /**
     * Get an array of all valid field values.
     *
     * @return array An array of valid field values.
     */
    public static function toArray(): array
    {
        return  array_column(Field::cases(), 'value');
    }
}
/**
 * @OA\Info(
 *   title="Based Movies Database API",
 *   version="1.0.0",
 *   @OA\Contact(
 *     email="leona202307@proton.me"
 *   )
 * )
 */
class MovieController
{
    protected static PDO $db;
    protected static $logger;

    public function __construct(PDO $db, $logger)
    {
        self::$db = $db;
        self::$logger = $logger;
    }

    /**
     * Get a list of movies.
     *
     * @return callable
     *
     * @throws \Throwable
     *
     * @OA\Get(
     *     path="/movies",
     *     summary="Get a list of movies",
     *     tags={"Movies"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Movie")
     *         )
     *     ),
     * )
     * Retrieve a list of all movies.
     *
     * @return callable A callable function that handles the request and returns a response.
     */
    public function index(): callable
    {
        return function (Request $req, Response $res): Response {
            try {
                $cachedData = false;
                if (method_exists($this, 'get')) {
                    $memcache = $this->get('memcache');
                    $cachedData = $memcache->get('index');
                }

                if ($cachedData === false) {
                    $data = Movie::all(MovieController::$db);
                    if (isset($memcache)) {
                        $memcache->set('index', $data, 3600);
                    }
                } else {
                    $data = $cachedData;
                }
                $payload = json_encode($data);
                $res->getBody()->write($payload);

                return $res->withHeader('Content-Type', 'application/json');
            } catch (\Throwable $e) {
                MovieController::$logger->error("request to /v1/movies " . $e->getMessage());
                $res->getBody()->write(json_encode(['error' => $e->getMessage()]));
                return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        };
    }

    /**
     * Read a specific movie by its UID.
     *
     * @return callable
     *
     * @throws \Throwable
     *
     * @OA\Get(
     *     path="/movies/{uid}",
     *     summary="Read a specific movie by UID",
     *     tags={"Movies"},
     *     @OA\Parameter(
     *         name="uid",
     *         in="path",
     *         required=true,
     *         description="Movie UID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Movie")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movie not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Movie not found")
     *         )
     *     ),
     * )
     *  * Read a movie by UID
     * @return callable The HTTP response with JSON data.
     */
    public function read(): callable
    {
        return function (Request $req, Response $res, array $args): Response {
            try {
                $uid = (int)$args['uid'];
                $cachedData = false;
                if (method_exists($this, 'get')) {
                    $memcache = $this->get('memcache');
                    $cachedData = $memcache->get($uid);
                }

                if ($cachedData === false) {
                    $data = Movie::findByUid(MovieController::$db, $uid);
                    if (isset($memcache)) {
                        $memcache->set($uid, $data, 3600);
                    }
                } else {
                    $data = $cachedData;
                }

                if (!$data) {
                    $res->getBody()->write(json_encode(['message' => 'Movie not found']));
                    return $res->withStatus(404)->withHeader('Content-Type', 'application/json');
                }

                $payload = json_encode($data);

                $res->getBody()->write($payload);

                return $res->withHeader('Content-Type', 'application/json');
            } catch (\Throwable $e) {
                MovieController::$logger->error("request to /v1/movie/{uid} " . $e->getMessage());

                $res->getBody()->write(json_encode(['error' => $e->getMessage()]));
                return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        };
    }

    /**
     * Create a new movie.
     *
     * @return callable
     *
     * @throws \Throwable
     *
     * @OA\Post(
     *     path="/movies",
     *     summary="Create a new movie",
     *     tags={"Movies"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Movie")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Movie created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Movie added successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Invalid input data")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to add movie",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Failed to add movie")
     *         )
     *     ),
     * )
     *  * Create a new movie.
     * @return callable The HTTP response with JSON data.
     */
    public function create(): callable
    {
        return function (Request $req, Response $res): Response {
            try {
                $rawJson = $req->getBody()->getContents();
                if (empty($rawJson)) {
                    $res->getBody()->write(json_encode(['error' => 'Invalid JSON data']));
                    return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
                $postData = json_decode($rawJson, true);

                $validatedData = MovieSanitizer::sanitize($postData);

                if (!$validatedData) {
                    $res->getBody()->write(json_encode(['message' => 'Invalid input data']));
                    return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
                }

                $result = Movie::create(MovieController::$db, $validatedData);

                if ($result) {
                    $res->getBody()->write(json_encode(['message' => 'Movie added successfully']));
                    return $res->withStatus(201)->withHeader('Content-Type', 'application/json');
                } else {
                    $res->getBody()->write(json_encode(['message' => 'Failed to add movie']));
                    return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
                }
            } catch (\Throwable $e) {
                MovieController::$logger->error("request to POST /v1/movies " . $e->getMessage());

                $res->getBody()->write(json_encode(['error' => $e->getMessage()]));
                return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        };
    }

    /**
     * @OA\Put(
     *     path="/movies/{id}",
     *     summary="Update a movie by ID",
     *     tags={"Movies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Movie ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MovieInput")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Movie updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Movie updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Invalid input data")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movie not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Movie not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to update movie",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Failed to update movie")
     *         )
     *     ),
     * )
     *  * Update a movie by ID using the PUT method.
     * @return callable The HTTP response with JSON data.
     */
    public function update(): callable
    {
        return function (Request $req, Response $res, array $args): Response {
            try {
                $uid = $args['uid'];

                $rawJson = $req->getBody()->getContents();
                if (empty($rawJson)) {
                    $res->getBody()->write(json_encode(['error' => 'Invalid JSON data']));
                    return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
                $postData = json_decode($rawJson, true);

                $validatedData = MovieSanitizer::sanitize($postData);

                if (!$validatedData) {
                    $res->getBody()->write(json_encode(['message' => 'Invalid input data']));
                    return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
                }

                $existingMovie = Movie::findByUid(MovieController::$db, $uid);

                if (!$existingMovie) {
                    $res->getBody()->write(json_encode(['message' => 'Movie not found']));
                    return $res->withStatus(404)->withHeader('Content-Type', 'application/json');
                }

                $result = Movie::updateById(MovieController::$db, $existingMovie['id'], $validatedData);

                if ($result) {
                    $res->getBody()->write(json_encode(['message' => 'Movie updated successfully']));
                    return $res->withStatus(200)->withHeader('Content-Type', 'application/json');
                } else {
                    $res->getBody()->write(json_encode(['message' => 'Failed to update movie']));
                    return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
                }
            } catch (\Throwable $e) {
                MovieController::$logger->error("request to PUT /v1/movies " . $e->getMessage());

                $res->getBody()->write(json_encode(['error' => $e->getMessage()]));
                return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        };
    }

    /**
     * @OA\Patch(
     *     path="v1/movies/{id}",
     *     summary="Patch/update a movie by ID",
     *     tags={"Movies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Movie ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MovieInput")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Movie updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Movie updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Invalid input data")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movie not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Movie not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to update movie",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Failed to update movie")
     *         )
     *     ),
     * )
     *
     * Update a movie by ID using the PATCH method.
     * @return callable The HTTP response with JSON data.
     */

    public function patch(): callable
    {
        return function (Request $req, Response $res, array $args): Response {
            try {
                $uid = $args['uid'];
                $rawJson = $req->getBody()->getContents();
                if (empty($rawJson)) {
                    $res->getBody()->write(json_encode(['error' => 'Invalid JSON data']));
                    return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
                $postData = json_decode($rawJson, true);

                $validatedData = MovieSanitizer::sanitize($postData);
                if (!$validatedData) {
                    $res->getBody()->write(json_encode(['message' => 'Invalid input data']));
                    return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
                }

                $existingMovie = Movie::findByUid(MovieController::$db, $uid);

                if (!$existingMovie) {
                    $res->getBody()->write(json_encode(['message' => 'Movie not found']));
                    return $res->withStatus(404)->withHeader('Content-Type', 'application/json');
                }

                $result = Movie::updateById(MovieController::$db, (int) $existingMovie['uid'], $validatedData);

                if ($result) {
                    $res->getBody()->write(json_encode(['message' => 'Movie updated successfully']));
                    return $res->withStatus(200)->withHeader('Content-Type', 'application/json');
                } else {
                    $res->getBody()->write(json_encode(['message' => 'Failed to update movie']));
                    return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
                }
            } catch (\Throwable $e) {
                MovieController::$logger->error("request to PATCH /v1/movies " . $e->getMessage());

                $res->getBody()->write(json_encode(['error' => $e->getMessage()]));
                return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        };
    }

    /**
     * @OA\Delete(
     *     path="/movies/{id}",
     *     summary="Delete a movie by ID",
     *     tags={"Movies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Movie ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Movie deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Movie deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movie not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Movie not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete movie",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Failed to delete movie")
     *         )
     *     ),
     * )
     *
     *  * Delete a movie by ID.
     * @return callable The HTTP response with JSON data.
     */
    public function delete(): callable
    {
        return (function (Request $req, Response $res, array $args): Response {
            $id = $args['uid'];
            $result = Movie::deleteById(MovieController::$db, $id);

            if ($result) {
                $res->getBody()->write(json_encode(['message' => 'Movie deleted successfully']));
                return $res->withStatus(200)->withHeader('Content-Type', 'application/json');
            } else {
                MovieController::$logger->error("request to DELETE /v1/movies ");
                $res->getBody()->write(json_encode(['message' => 'Failed to delete movie']));
                return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        });
    }
    /**
     * @OA\Get(
     *     path="/moviesPerPage",
     *     summary="Get a paginated list of movies.",
     *     tags={"Movies"},
     *     @OA\Parameter(
     *         name="numberPerPage",
     *         in="query",
     *         description="Number of movies per page.",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful response",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Error response",
     *         @OA\JsonContent(type="object", @OA\Property(property="error", type="string"))
     *     )
     * )
     *  * Get a paginated list of movies.
     * @return callable The HTTP response with JSON data.
     */
    public function moviesPerPage(): callable
    {
        return (function (Request $req, Response $res, array $args): Response {
            try {
                $numberPerPage = is_numeric($args['numberPerPage']) ? $args['numberPerPage'] : throw new Exception("Not a number");
                $cachedData = false;
                if (method_exists($this, 'get')) {
                    $memcache = $this->get('memcache');
                    $cachedData = $memcache->get("index_page" . $numberPerPage);
                }

                if ($cachedData === false) {
                    $data = Movie::byNumberPerPage(MovieController::$db, (int) $numberPerPage);
                    if (isset($memcache)) {
                        $memcache->set("index_page" . $numberPerPage, $data, 3600);
                    }
                } else {
                    $data = $cachedData;
                }
                $payload = json_encode($data);
                $res->getBody()->write($payload);
                return $res->withHeader('Content-Type', 'application/json');
            } catch (\Throwable $e) {
                MovieController::$logger->error("request to  /v1/movies/# " . $e->getMessage());

                $res->getBody()->write(json_encode(["error" => $e->getMessage()]));
                return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        });
    }

    /**
     * @OA\Get(
     *     path="/movies",
     *     summary="Get a paginated and sorted list of movies.",
     *     tags={"Movies"},
     *     @OA\Parameter(
     *         name="numberPerPage",
     *         in="query",
     *         description="Number of movies per page.",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Field to sort by.",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful response",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Error response",
     *         @OA\JsonContent(type="object", @OA\Property(property="error", type="string"))
     *     )
     * )
     *
     *  * Get a paginated and sorted list of movies.
     * @return callable The HTTP response with JSON data.
     */
    public function moviesPerPageAndSort(): callable
    {
        return (function (Request $req, Response $res, array $args): Response {
            try {
                $numberPerPage = is_numeric($args['numberPerPage']) ? $args['numberPerPage'] : throw new Exception("Not a number");
                $sortBy = Field::isValid($args['sort']) ? $args['sort'] : throw new Exception("Not a valid sort option can be only : " . json_encode(Field::toArray()));
                $cachedData = false;
                if (method_exists($this, 'get')) {
                    $memcache = $this->get('memcache');
                    $cachedData = $memcache->get("index_page" . $numberPerPage);
                }

                if ($cachedData === false) {
                    $data = Movie::byNumberPerPageAndSort(MovieController::$db, (int) $numberPerPage, $sortBy);
                    if (isset($memcache)) {
                        $memcache->set("index_page" . $numberPerPage . "_"  . $sortBy, $data, 3600);
                    }
                } else {
                    $data = $cachedData;
                }

                $payload = json_encode($data);
                $res->getBody()->write($payload);
                return $res->withHeader('Content-Type', 'application/json');
            } catch (\Throwable $e) {
                MovieController::$logger->error("request to  /v1/movies/#.sort " . $e->getMessage());

                $res->getBody()->write(json_encode(["error" => $e->getMessage()]));
                return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        });
    }

    /**
     * Define a route callback for fetching movies with pagination and filtering.
     *
     * @OA\Get(
     *     path="/v1/movies/{numberPerPage}/filter/{filter}",
     *     summary="Retrieve a list of movies with pagination and a filter.",
     *     tags={"Movies"},
     *     @OA\Parameter(
     *         name="numberPerPage",
     *         in="path",
     *         description="The number of items to display per page.",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filter",
     *         in="path",
     *         description="The filter option to apply to the movies.",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Movie")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     )
     * )
     *  * Get a paginated and filtered list of movies.
     * @return callable The HTTP response with JSON data.
     */
    public function moviesPerPageAndFilter(): callable
    {
        return (function (Request $req, Response $res, array $args): Response {
            try {
                $numberPerPage = is_numeric($args['numberPerPage']) ? $args['numberPerPage'] : throw new Exception("Not a number");
                $filter = Field::isValid($args['filter']) ? $args['filter'] : throw new Exception("Not a valid filter option can be only : " . json_encode(Field::toArray()));
                $cachedData = false;
                if (method_exists($this, 'get')) {
                    $memcache = $this->get('memcache');
                    $cachedData = $memcache->get("index_page" . $numberPerPage);
                }
                if ($cachedData === false) {
                    $data = Movie::byNumberPerPageAndFilter(MovieController::$db, (int) $numberPerPage, $filter);
                    if (isset($memcache)) {
                        $memcache->set("index_page" . $numberPerPage . "_"  . $filter, $data, 3600);
                    }
                } else {
                    $data = $cachedData;
                }

                $payload = json_encode($data);
                $res->getBody()->write($payload);
                return $res->withHeader('Content-Type', 'application/json');
            } catch (\Throwable $e) {
                MovieController::$logger->error("request to  /v1/movies/#/filter " . $e->getMessage());

                $res->getBody()->write(json_encode(["error" => $e->getMessage()]));
                return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        });
    }

    /**
     * @OA\Get(
     *     path="/movies/{numberPerPage}/search/{query}",
     *     summary="Retrieve a list of movies with pagination and search functionality.",
     *     tags={"Movies"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="The page number (default is 1).",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="numberPerPage",
     *         in="path",
     *         description="The number of items to display per page.",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="query",
     *         in="path",
     *         description="The search term to filter movies by title.",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Movie")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     )
     * )
     *  * Get a paginated and searched list of movies.
     * @return callable The HTTP response with JSON data.
     */
    public function moviesPerPageAndSearch(): callable
    {
        return (function (Request $req, Response $res, array $args): Response {
            try {
                $numberPerPage = is_numeric($args['numberPerPage']) ? $args['numberPerPage'] : throw new Exception("Not a number");
                $search = isset($args['search']) ? $args['search'] : throw new Exception("Not a valid search query");
                $cachedData = false;
                if (method_exists($this, 'get')) {
                    $memcache = $this->get('memcache');
                    $cachedData = $memcache->get("index_page" . $numberPerPage);
                }
                if ($cachedData === false) {
                    $data = Movie::byNumberPerPageAndFilter(MovieController::$db, (int) $numberPerPage, $search);
                    if (isset($memcache)) {
                        $memcache->set("index_page" . $numberPerPage . "_"  . $search, $data, 3600);
                    }
                } else {
                    $data = $cachedData;
                }

                $data = Movie::byNumberPerPageAndSearch(MovieController::$db, (int) $numberPerPage, $search);
                $payload = json_encode($data);
                $res->getBody()->write($payload);
                return $res->withHeader('Content-Type', 'application/json');
            } catch (\Throwable $e) {
                MovieController::$logger->error("request to  /v1/movies/#/search " . $e->getMessage());

                $res->getBody()->write(json_encode(["error" => $e->getMessage()]));
                return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        });
    }
}
