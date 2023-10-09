<?php

declare(strict_types=1);

namespace App\Application\Controllers\Method;

require_once(__DIR__ . "/../../../../bootstrap.php");

use App\Application\Controllers\Controller;
use App\Application\Controllers\Interfaces\ActivatableInterface;
use App\Application\Controllers\Interfaces\CrudInterface;
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
    public function read(): callable
    {
        return function (Request $req, Response $res): Response {
            try {
                $method = self::$entityManager->getRepository(Method::class);
                $data = $method->findAll();
                $payload = array_map(fn ($mthd) => (array) $mthd, $data);
                return Controller::jsonResponse($res, $payload);
            } catch (\Throwable $e) {
                Controller::logError($e, "GET /v1/methods");
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500);
            }
        };
    }
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
                $method->setName(htmlspecialchars($postData['name']) ?? "");
                $method->setDescription(htmlspecialchars($postData['description'])?? "");
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
                    return Controller::jsonResponse($res, ['error' => 'Customer not found'], 404);
                }


                if (isset($postData['name'])) {
                    $method->setName(htmlspecialchars($postData['name'])?? "");
                }
            
                if (isset($postData['description'])) {
                    $method->setDescription(htmlspecialchars($postData['description'])?? "");
                }


                self::$entityManager->flush();

                return Controller::jsonResponse($res, ['message' => 'Method updated successfully'], 200);
            } catch (\Throwable $e) {
                Controller::logError($e, "PUT /v1/methods");
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500)
                    ->withHeader('Content-Type', 'application/json'); // Set the content type header
            }
        };
    }


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
                    $response = ['body' => ['error' => 'Method not found'], 'statusCode' => 404];
                }
                return Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (\Throwable $e) {
                Controller::logError($e, "DELETE /v1/methods/" . $args[0]);
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500);
            }
        };
    }
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
                    $response = ['body' => ['error' => 'method not found'], 'statusCode' => 404];
                }
                return Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (\Throwable $e) {
                Controller::logError($e, "GET /v1/methods/reactivate" . $args[0]);
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500);
            }
        };
    }

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
                    $response = ['body' => ['error' => 'Method not found'], 'statusCode' => 404];
                }
                return Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (\Throwable $e) {
                Controller::logError($e, "GET /v1/methods/deactivate" . $args[0]);
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500);
            }
        };
    }
}
