<?php

declare(strict_types=1);

namespace App\Application\Controllers\Movie;

use PDO;
use App\Application\Models\Movie;
use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Annotations as OA;

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

    protected PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
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
                /** @var PDO $db */
    
                $sth = $this->get($this->db ?? PDO::class)->prepare("SELECT * FROM movies");
                $data = $sth->fetchAll(PDO::FETCH_ASSOC);
                if (!$res) {
                    $res = new Response();
                }
                print_r($data);
                $payload = json_encode($data);

                $res->getBody()->write($payload);

                return $res->withHeader('Content-Type', 'application/json');
            } catch (\Throwable $e) {
                // Handle exceptions here and return an appropriate error response.
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
     *  * Read a movie by UID.
     *
     * @param Request $req The HTTP request object.
     * @param Response $res The HTTP response object.
     * @param array $args The route arguments.
     *
     * @return Response The HTTP response with JSON data.
     */
    public function read(): callable
    {
        return function (Request $req, Response $res, array $args): Response {
            try {
                $uid =  (int) $args['uid'];
                $data = Movie::findByUid($this->get(PDO::class), $uid);

                if (!$data) {
                    $res->getBody()->write(json_encode(['message' => 'Movie not found']));
                    return $res->withStatus(404)->withHeader('Content-Type', 'application/json');
                }

                $payload = json_encode($data);

                $res->getBody()->write($payload);

                return $res->withHeader('Content-Type', 'application/json');
            } catch (\Throwable $e) {
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
     *
     * @param Request $req The HTTP request object.
     * @param Response $res The HTTP response object.
     *
     * @return Response The HTTP response with JSON data.
     */
    public function create(): callable
    {
        return function (Request $req, Response $res): Response {
            try {
                $postData = $req->getParsedBody();
                $validatedData = validateAndSanitizeMovieData($postData);

                if (!$validatedData) {
                    $res->getBody()->write(json_encode(['message' => 'Invalid input data']));
                    return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
                }

                $result = Movie::create($this->get(PDO::class), $validatedData);

                if ($result) {
                    $res->getBody()->write(json_encode(['message' => 'Movie added successfully']));
                    return $res->withStatus(201)->withHeader('Content-Type', 'application/json');
                } else {
                    $res->getBody()->write(json_encode(['message' => 'Failed to add movie']));
                    return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
                }
            } catch (\Throwable $e) {
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
     *
     * @param Request $req The HTTP request object.
     * @param Response $res The HTTP response object.
     * @param array $args The route arguments.
     *
     * @return Response The HTTP response with JSON data.
     */
    public function update(): callable
    {
        return function (Request $req, Response $res, array $args): Response {
            try {
                $id = $args['id'];
                $postData = $req->getParsedBody();
                $validatedData = validateAndSanitizeMovieData($postData);

                if (!$validatedData) {
                    $res->getBody()->write(json_encode(['message' => 'Invalid input data']));
                    return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
                }

                $existingMovie = Movie::findById($this->get(PDO::class), $id);

                if (!$existingMovie) {
                    $res->getBody()->write(json_encode(['message' => 'Movie not found']));
                    return $res->withStatus(404)->withHeader('Content-Type', 'application/json');
                }

                $result = Movie::updateById($this->get(PDO::class), $id, $validatedData);

                if ($result) {
                    $res->getBody()->write(json_encode(['message' => 'Movie updated successfully']));
                    return $res->withStatus(200)->withHeader('Content-Type', 'application/json');
                } else {
                    $res->getBody()->write(json_encode(['message' => 'Failed to update movie']));
                    return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
                }
            } catch (\Throwable $e) {
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
     *
     * @param Request $req The HTTP request object.
     * @param Response $res The HTTP response object.
     * @param array $args The route arguments.
     *
     * @return Response The HTTP response with JSON data.
     */

    public function patch(): callable
    {
        return function (Request $req, Response $res, array $args): Response {
            try {
                $id = $args['id'];
                $postData = $req->getParsedBody();
                $validatedData = validateAndSanitizeMovieData($postData);

                if (!$validatedData) {
                    $res->getBody()->write(json_encode(['message' => 'Invalid input data']));
                    return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
                }

                $existingMovie = Movie::findById($this->get(PDO::class), $id);

                if (!$existingMovie) {
                    $res->getBody()->write(json_encode(['message' => 'Movie not found']));
                    return $res->withStatus(404)->withHeader('Content-Type', 'application/json');
                }

                $result = Movie::updateById($this->get(PDO::class), $id, $validatedData);

                if ($result) {
                    $res->getBody()->write(json_encode(['message' => 'Movie updated successfully']));
                    return $res->withStatus(200)->withHeader('Content-Type', 'application/json');
                } else {
                    $res->getBody()->write(json_encode(['message' => 'Failed to update movie']));
                    return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
                }
            } catch (\Throwable $e) {
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
     *
     * @param Request $req The HTTP request object.
     * @param Response $res The HTTP response object.
     * @param array $args The route arguments.
     *
     * @return Response The HTTP response with JSON data.
     */
    public function delete(): callable
    {
        return (function (Request $req, Response $res, array $args): Response {
            $id = $args['id'];
            $result = Movie::deleteById($this->get(PDO::class), $id);

            if ($result) {
                $res->getBody()->write(json_encode(['message' => 'Movie deleted successfully']));
                return $res->withStatus(200)->withHeader('Content-Type', 'application/json');
            } else {
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
     *
     * @param Request $req The HTTP request object.
     * @param Response $res The HTTP response object.
     * @param array $args The route arguments.
     *
     * @return Response The HTTP response with JSON data.
     */
    public function moviesPerPage(): callable
    {
        return (function (Request $req, Response $res, array $args): Response {
            try {
                $numberPerPage = is_numeric($args['numberPerPage']) ? $args['numberPerPage'] : throw new Exception("Not a number");
                $data = Movie::byNumberPerPage($this->get(PDO::class), (int) $numberPerPage);
                $payload = json_encode($data);
                $res->getBody()->write($payload);
                return $res->withHeader('Content-Type', 'application/json');
            } catch (\Throwable $e) {
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
     *
     * @param Request $req The HTTP request object.
     * @param Response $res The HTTP response object.
     * @param array $args The route arguments.
     *
     * @return Response The HTTP response with JSON data.
     */
    public function moviesPerPageAndSort(): callable
    {
        return (function (Request $req, Response $res, array $args): Response {
            try {
                $numberPerPage = is_numeric($args['numberPerPage']) ? $args['numberPerPage'] : throw new Exception("Not a number");
                $sortBy = Field::isValid($args['sort']) ? $args['sort'] : throw new Exception("Not a valid sort option can be only : " . json_encode(Field::toArray()));
                $data = Movie::byNumberPerPageAndSort($this->get(PDO::class), (int) $numberPerPage, $sortBy);
                $payload = json_encode($data);
                $res->getBody()->write($payload);
                return $res->withHeader('Content-Type', 'application/json');
            } catch (\Throwable $e) {
                $res->getBody()->write(json_encode(["error" => $e->getMessage()]));
                return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        });
    }
}
