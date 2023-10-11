<?php

declare(strict_types=1);

namespace App\Application\Controllers\Method;

require_once(__DIR__ . "/../../../../bootstrap.php");

use App\Application\Controllers\Controller;
use App\Application\Controllers\Interfaces\ActivatableInterface;
use App\Application\Controllers\Interfaces\CrudInterface;
use App\Application\Exceptions\MethodNotFoundException;
use App\Application\Models\Method;
use App\Utils\Sanitizers\CustomerSanitizer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use OpenApi\Annotations as OA;
use Exception;
use PDO;
use Psr\Log\LoggerInterface;

class MethodController extends Controller implements CrudInterface, ActivatableInterface
{

    /**
     * @OA\Get(
     *     path="/v1/methods",
     *     summary="Get all methods",
     *     tags={"Methods"},
     *     @OA\Response(
     *         response=200,
     *         description="List of methods",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Method")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )

     */
    public function read(): callable
    {
        return function (Request $req, Response $res): Response {
            try {
                $method = self::$entityManager->getRepository(Method::class);
                $data = $method->findAll();
                $payload = array_map(fn (Method $mthd) =>  $mthd->toArray(), $data);
                return Controller::jsonResponse($res, $payload);
            } catch (\Throwable $e) {
                Controller::logError($e, "GET /v1/methods");
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500);
            }
        };
    }
    /**
     * @OA\Post(
     *     path="/v1/methods",
     *     summary="Create a new method",
     *     tags={"Methods"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Method")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Method added successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function create(): callable
    {
        return function (Request $req, Response $res): Response {
            try {
                $rawJson = $req->getBody()->getContents();

                if (empty($rawJson)) {
                    $response = ['body' => ['error' => 'Invalid JSON data'], 'statusCode' => 400];
                }

                $postData = json_decode($rawJson, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $response = ['body' => ['error' => 'Invalid JSON format'], 'statusCode' => 400];
                }

                $entityManager = self::$entityManager;
                $method = new Method();
                $method->setName(htmlspecialchars($postData['name']));
                $method->setDescription(htmlspecialchars($postData['description']));
                $method->setActive(true);


                $entityManager->persist($method);
                $entityManager->flush();

                $response = ['body' => ['message' => 'Method added successfully '], 'statusCode' => 201];

                return  Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (\Throwable $e) {
                Controller::logError($e, "POST /v1/methods");
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500);
            }
        };
    }

    /**
     * @OA\Put(
     *     path="/v1/methods/{methodId}",
     *     summary="Update a method",
     *     tags={"Methods"},
     *     @OA\Parameter(
     *         name="methodId",
     *         in="path",
     *         description="ID of the method to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Method")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Method updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Method not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function update(): callable
    {
        return function (Request $req, Response $res, array $args): Response {
            try {
                $rawJson = $req->getBody()->getContents();

                if (empty($rawJson)) {
                    return Controller::jsonResponse($res, ['error' => 'Invalid JSON data'], 400);
                }

                $postData = json_decode($rawJson, true);

                $methodId = (int) $args[0];
                $method = self::$entityManager->getRepository(Method::class)->find($methodId);

                if (!$method) {
                    throw new MethodNotFoundException();
                }


                if (isset($postData['name'])) {
                    $method->setName(htmlspecialchars($postData['name']));
                }

                if (isset($postData['description'])) {
                    $method->setDescription(htmlspecialchars($postData['description']));
                }


                self::$entityManager->flush();

                return Controller::jsonResponse($res, ['message' => 'Method updated successfully'], 200);
            } catch (MethodNotFoundException $e) {
                return  Controller::jsonResponse($res, ['error' => $e->getMessage()], $e->getCode());
            } catch (\Throwable $e) {
                Controller::logError($e, "PUT /v1/methods");
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500)
                    ->withHeader('Content-Type', 'application/json'); // Set the content type header
            }
        };
    }
    /**
     * @OA\Delete(
     *     path="/v1/methods/{methodId}",
     *     summary="Delete a method",
     *     tags={"Methods"},
     *     @OA\Parameter(
     *         name="methodId",
     *         in="path",
     *         description="ID of the method to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Method deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Method not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    public function delete(): callable
    {
        return function (Request $req, Response $res, array $args): Response {
            try {
                if (is_numeric($args[0])) {
                    $method = Controller::$entityManager->find(Method::class, $args[0]);
                } elseif (filter_var($args[0], FILTER_VALIDATE_EMAIL)) {
                    $method = Controller::$entityManager->getRepository(Method::class)->findOneBy(['email' => $args[0]]);
                }
                if (isset($method)) {
                    Controller::$entityManager->remove($method);
                    Controller::$entityManager->flush();
                    $response = ['body' => ['message' => 'Method deleted successfully.'], 'statusCode' => 200];
                } else {
                    throw new MethodNotFoundException();
                }
                return Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (MethodNotFoundException $e) {
                return  Controller::jsonResponse($res, ['error' => $e->getMessage()], $e->getCode());
            } catch (\Throwable $e) {
                Controller::logError($e, "DELETE /v1/methods/" . $args[0]);
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500);
            }
        };
    }
    /**
     * @OA\Post(
     *     path="/v1/methods/reactivate/{methodId}",
     *     summary="Reactivate a method",
     *     tags={"Methods"},
     *     @OA\Parameter(
     *         name="methodId",
     *         in="path",
     *         description="ID of the method to reactivate",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Method reactivated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Method not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function reactivate(): callable
    {
        return function (Request $req, Response $res, array $args): Response {
            try {
                if (is_numeric($args[0])) {
                    $method = Controller::$entityManager->find(Method::class, $args[0]);
                } elseif (filter_var($args[0], FILTER_VALIDATE_EMAIL)) {
                    $method = Controller::$entityManager->getRepository(Method::class)->findOneBy(['email' => $args[0]]);
                }
                if (isset($method)) {
                    $method->setActive(true);
                    Controller::$entityManager->flush();
                    $response = ['body' => ['message' => 'method deleted successfully.'], 'statusCode' => 200];
                } else {
                    throw new MethodNotFoundException();
                }
                return Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (MethodNotFoundException $e) {
                return  Controller::jsonResponse($res, ['error' => $e->getMessage()], $e->getCode());
            } catch (\Throwable $e) {
                Controller::logError($e, "GET /v1/methods/reactivate" . $args[0]);
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500);
            }
        };
    }
    /**
     * @OA\Post(
     *     path="/v1/methods/deactivate/{methodId}",
     *     summary="Deactivate a method",
     *     tags={"Methods"},
     *     @OA\Parameter(
     *         name="methodId",
     *         in="path",
     *         description="ID of the method to deactivate",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Method deactivated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Method not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function deactivate(): callable
    {
        return function (Request $req, Response $res, array $args): Response {
            try {
                if (is_numeric($args[0])) {
                    $method = Controller::$entityManager->find(Method::class, $args[0]);
                } elseif (filter_var($args[0], FILTER_VALIDATE_EMAIL)) {
                    $method = Controller::$entityManager->getRepository(Method::class)->findOneBy(['email' => $args[0]]);
                }
                if (isset($method)) {
                    $method->setActive(false);
                    Controller::$entityManager->flush();
                    $response = ['body' => ['message' => 'Method deactivated successfully.'], 'statusCode' => 200];
                } else {
                    throw new MethodNotFoundException();
                }
                return Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (MethodNotFoundException $e) {
                return  Controller::jsonResponse($res, ['error' => $e->getMessage()], $e->getCode());
            } catch (\Throwable $e) {
                Controller::logError($e, "GET /v1/methods/deactivate" . $args[0]);
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500);
            }
        };
    }
}
