<?php

declare(strict_types=1);

namespace App\Application\Controllers\Customer;

require_once(__DIR__ . "/../../../../bootstrap.php");

use App\Application\Controllers\Controller;
use App\Application\Controllers\Interfaces\ActivatableInterface;
use App\Application\Controllers\Interfaces\CrudInterface;
use App\Application\Models\Customer;
use App\Utils\Sanitizers\CustomerSanitizer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use OpenApi\Annotations as OA;
use Exception;
use PDO;
use Psr\Log\LoggerInterface;

class CustomerController extends Controller implements CrudInterface, ActivatableInterface
{
    public function read(): callable
    {
        return function (Request $req, Response $res): Response {
            try {
                $customer = self::$entityManager->getRepository(Customer::class);
                $data = $customer->findAll();
                $payload = array_map(fn ($pmnt) => (array) $pmnt, $data);
                return Controller::jsonResponse($res, $payload);
            } catch (\Throwable $e) {
                Controller::logError($e, "GET /v1/customers");
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
                if (json_last_error() !== JSON_ERROR_NONE || is_null($postData)) {
                    $response = ['body' => ['error' => 'Invalid JSON format'], 'statusCode' => 400];
                }
                $validatedData = isset($postData) ? CustomerSanitizer::sanitize($postData) : null;
                if (!$validatedData) {
                    $response = ['body' => ['error' => 'Invalid input data'], 'statusCode' => 400];
                } else {
                    $entityManager = self::$entityManager;
                    $customer = new Customer();
                    $customer->setName($validatedData['name']);
                    $customer->setEmail($validatedData['email']);
                    $customer->setBalance($validatedData['balance'] ?? 0);
                    $customer->setActive(true);
                    $payload = [

                        "email" => $customer->getEmail(),
                        "name" => $customer->getName(),
                    ];

                    $secretKey =  $_ENV["JWT_SECRET"] ?? "testing_key";
                    $customer->setJWT(JWT::encode($payload, $secretKey, 'HS256'));
                    $entityManager->persist($customer);
                    $entityManager->flush();


                    $response = ['body' => ['message' => 'Customer added successfully your jwt: ' . $customer->getJwt()], 'statusCode' => 201];
                }

                return  Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (\Throwable $e) {
                Controller::logError($e, "POST /v1/customers");
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

                $customerId = (int) $args[0];
                $customer = self::$entityManager->getRepository(Customer::class)->find($customerId);

                if (!$customer) {
                    return Controller::jsonResponse($res, ['error' => 'Customer not found'], 404);
                }

                $validatedData = CustomerSanitizer::sanitize($postData, true);

                if (!$validatedData) {
                    return Controller::jsonResponse($res, ['error' => 'Invalid input data'], 400);
                }

                if (isset($validatedData['name'])) {
                    $customer->setName($validatedData['name']);
                }
                if (isset($validatedData['email'])) {
                    $customer->setEmail($validatedData['email']);
                }
                if (isset($validatedData['balance'])) {
                    $customer->setBalance($validatedData['balance']);
                }


                self::$entityManager->flush();

                return Controller::jsonResponse($res, ['message' => 'Customer updated successfully'], 200);
            } catch (\Throwable $e) {
                Controller::logError($e, "PUT /v1/customers");
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
                    $customer = CustomerController::$entityManager->find(Customer::class, $args[0]);
                } elseif (filter_var($args[0], FILTER_VALIDATE_EMAIL)) {
                    $customer = CustomerController::$entityManager->getRepository(Customer::class)->findOneBy(['email' => $args[0]]);
                }
                if (isset($customer)) {
                    CustomerController::$entityManager->remove($customer);
                    CustomerController::$entityManager->flush();
                    $response = ['body' => ['message' => 'Customer deleted successfully.'], 'statusCode' => 200];
                } else {
                    $response = ['body' => ['error' => 'Customer not found'], 'statusCode' => 404];
                }
                return Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (\Throwable $e) {
                Controller::logError($e, "DELETE /v1/customers/" . $args[0]);
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500);
            }
        };
    }
    public function reactivate(): callable
    {
        return function (Request $req, Response $res, array $args): Response {
            try {
                if (is_numeric($args[0])) {
                    $customer = CustomerController::$entityManager->find(Customer::class, $args[0]);
                } elseif (filter_var($args[0], FILTER_VALIDATE_EMAIL)) {
                    $customer = CustomerController::$entityManager->getRepository(Customer::class)->findOneBy(['email' => $args[0]]);
                }
                if (isset($customer)) {
                    $customer->setActive(true);
                    CustomerController::$entityManager->flush();
                    $response = ['body' => ['message' => 'Customer deleted successfully.'], 'statusCode' => 200];
                } else {
                    $response = ['body' => ['error' => 'Customer not found'], 'statusCode' => 404];
                }
                return Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (\Throwable $e) {
                Controller::logError($e, "GET /v1/customers/reactivate" . $args[0]);
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500);
            }
        };
    }

    public function deactivate(): callable
    {
        return function (Request $req, Response $res, array $args): Response {
            try {
                if (is_numeric($args[0])) {
                    $customer = CustomerController::$entityManager->find(Customer::class, $args[0]);
                } elseif (filter_var($args[0], FILTER_VALIDATE_EMAIL)) {
                    $customer = CustomerController::$entityManager->getRepository(Customer::class)->findOneBy(['email' => $args[0]]);
                }
                if (isset($customer)) {
                    $customer->setActive(false);
                    CustomerController::$entityManager->flush();
                    $response = ['body' => ['message' => 'Customer deactivated successfully.'], 'statusCode' => 200];
                } else {
                    $response = ['body' => ['error' => 'Customer not found'], 'statusCode' => 404];
                }
                return Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (\Throwable $e) {
                Controller::logError($e, "GET /v1/customers/deactivate" . $args[0]);
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500);
            }
        };
    }
}
