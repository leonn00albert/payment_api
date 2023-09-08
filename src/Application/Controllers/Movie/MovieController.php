<?php

declare(strict_types=1);

namespace App\Application\Controllers\Movie;

use PDO;
use App\Application\Models\Movie;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Annotations as OA;

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
     */
    public function index(): callable
    {
        return function (Request $req, Response $res): Response {
            try {
                /** @var PDO $db */
                $db = $this->get(PDO::class);

                $sth = $db->prepare("SELECT * FROM movies");
                $sth->execute();

                $data = $sth->fetchAll(PDO::FETCH_ASSOC);

                $payload = json_encode($data);

                $res->getBody()->write($payload);

                return $res->withHeader('Content-Type', 'application/json');
            } catch (\Throwable $e) {
                // Handle exceptions here and return an appropriate error response.
                return $res->withStatus(500)->withJson(['error' => $e->getMessage()]);
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
     */
    public function read(): callable
    {
        return function (Request $req, Response $res, array $args): Response {
            try {
                $uid = $args['uid'];
                $data = Movie::findByUid($this->get(PDO::class), (int) $uid);

                if (!$data) {
                    $res->getBody()->write(json_encode(['message' => 'Movie not found']));
                    return $res->withStatus(404)->withHeader('Content-Type', 'application/json');
                }

                $payload = json_encode($data);

                $res->getBody()->write($payload);

                return $res->withHeader('Content-Type', 'application/json');
            } catch (\Throwable $e) {
                return $res->withStatus(500)->withJson(['error' => $e->getMessage()]);
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
     */
    public function create()
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
                return $res->withStatus(500)->withJson(['error' => $e->getMessage()]);
            }
        };
    }

    /**
     * Update a movie by ID.
     *
     * @return callable
     *
     * @throws \Throwable
     *
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
     */
    public function update()
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
                return $res->withStatus(500)->withJson(['error' => $e->getMessage()]);
            }
        };
    }

    /**
     * Patch/update a movie by ID.
     *
     * @return callable
     *
     * @throws \Throwable
     *
     * @OA\Patch(
     *     path="/movies/{id}",
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
     */
    public function patch()
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
                return $res->withStatus(500)->withJson(['error' => $e->getMessage()]);
            }
        };
    }

    /**
     * Delete a movie by ID.
     *
     * @return callable
     *
     * @throws \Throwable
     *
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
     */
    public function delete()
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
}
