<?php

declare(strict_types=1);

namespace App\Application\Controllers\Customer;

require_once(__DIR__ . "/../../../../bootstrap.php");

use App\Application\Controllers\Controller;
use App\Application\Controllers\Interfaces\ActivatableInterface;
use App\Application\Controllers\Interfaces\CrudInterface;
use App\Application\Exceptions\CustomerNotFoundException;
use App\Application\Models\Customer;
use App\Utils\Errors\Errors;
use App\Utils\Sanitizers\CustomerSanitizer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use OpenApi\Annotations as OA;
use Exception;
use PDO;
use Psr\Log\LoggerInterface;

/**
 * @OA\Info(
 *     title="Payment API",
 *     version="1.0.0",
 *     description="an api for payments"
 * )
 */

class CustomerController extends Controller implements CrudInterface, ActivatableInterface
{

    /**
     * @OA\Get(
     *     path="/v1/customers",
     *     summary="Retrieve a list of customers",
     *     tags={"Customers"},
     *     @OA\Response(
     *         response=200,
     *         description="List of customers",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Customer")
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
                $customerRepository = self::$entityManager->getRepository(Customer::class);
                $customers = $customerRepository->findAll();
                
                $customerData = array_map(fn (Customer $customer) => $customer->toArray(), $customers);
    
                return Controller::jsonResponse($res, $customerData);
            } catch (\Throwable $e) {
                Controller::logError($e, "GET /v1/customers");
                return Controller::jsonResponse($res, ['error' => Errors::handleErrorCode($e->getCode())], 500);
            }
        };
    }
    /**
     * @OA\Post(
     *     path="/v1/customers",
     *     summary="Create a new customer",
     *     tags={"Customers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Customer added successfully",
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
                return Controller::jsonResponse($res, ['error' => Errors::handleErrorCode($e->getCode())], 500);
            }
        };
    }

    /**
     * @OA\Put(
     *     path="/v1/customers/{customerId}",
     *     summary="Update a customer",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="customerId",
     *         in="path",
     *         description="ID of the customer to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer updated successfully",
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
     *         description="Customer not found",
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

                $customerId = (int) $args[0];
                $customer = self::$entityManager->getRepository(Customer::class)->find($customerId);

                if (!$customer) {
                    throw new CustomerNotFoundException();
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
            } catch (CustomerNotFoundException $e) {
                return  Controller::jsonResponse($res, ['error' => $e->getMessage()], $e->getCode());
            } catch (\Throwable $e) {
                Controller::logError($e, "PUT /v1/customers");
                return Controller::jsonResponse($res, ['error' => Errors::handleErrorCode($e->getCode())], 500);
            }
        };
    }
    /**
     * @OA\Delete(
     *     path="/v1/customers/{customerId}",
     *     summary="Delete a customer",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="customerId",
     *         in="path",
     *         description="ID of the customer to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found",
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
                    $customer = CustomerController::$entityManager->find(Customer::class, $args[0]);
                } elseif (filter_var($args[0], FILTER_VALIDATE_EMAIL)) {
                    $customer = CustomerController::$entityManager->getRepository(Customer::class)->findOneBy(['email' => $args[0]]);
                }
                if (isset($customer)) {
                    CustomerController::$entityManager->remove($customer);
                    CustomerController::$entityManager->flush();
                    $response = ['body' => ['message' => 'Customer deleted successfully.'], 'statusCode' => 200];
                } else {
                    throw new CustomerNotFoundException();
                }
                return Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (CustomerNotFoundException $e) {
                return  Controller::jsonResponse($res, ['error' => $e->getMessage()], $e->getCode());
            } catch (\Throwable $e) {
                Controller::logError($e, "DELETE /v1/customers/" . $args[0]);
                return Controller::jsonResponse($res, ['error' => Errors::handleErrorCode($e->getCode())], 500);
            }
        };
    }
    /**
     * @OA\Put(
     *     path="/v1/customers/reactivate/{customerId}",
     *     summary="Reactivate a customer",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="customerId",
     *         in="path",
     *         description="ID of the customer to reactivate",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer reactivated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found",
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
                    $customer = CustomerController::$entityManager->find(Customer::class, $args[0]);
                } elseif (filter_var($args[0], FILTER_VALIDATE_EMAIL)) {
                    $customer = CustomerController::$entityManager->getRepository(Customer::class)->findOneBy(['email' => $args[0]]);
                }
                if (isset($customer)) {
                    $customer->setActive(true);
                    CustomerController::$entityManager->flush();
                    $response = ['body' => ['message' => 'Customer deleted successfully.'], 'statusCode' => 200];
                } else {
                    throw new CustomerNotFoundException();
                }
                return Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (CustomerNotFoundException $e) {
                return  Controller::jsonResponse($res, ['error' => $e->getMessage()], $e->getCode());
            } catch (\Throwable $e) {
                Controller::logError($e, "GET /v1/customers/reactivate" . $args[0]);
                return Controller::jsonResponse($res, ['error' => Errors::handleErrorCode($e->getCode())], 500);
            }
        };
    }
    /**
     * @OA\Put(
     *     path="/v1/customers/deactivate/{customerId}",
     *     summary="Deactivate a customer",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="customerId",
     *         in="path",
     *         description="ID of the customer to deactivate",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer deactivated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found",
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
                    $customer = CustomerController::$entityManager->find(Customer::class, $args[0]);
                } elseif (filter_var($args[0], FILTER_VALIDATE_EMAIL)) {
                    $customer = CustomerController::$entityManager->getRepository(Customer::class)->findOneBy(['email' => $args[0]]);
                }
                if (isset($customer)) {
                    $customer->setActive(false);
                    CustomerController::$entityManager->flush();
                    $response = ['body' => ['message' => 'Customer deactivated successfully.'], 'statusCode' => 200];
                } else {
                    throw new CustomerNotFoundException();
                }
                return Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (CustomerNotFoundException $e) {
                return  Controller::jsonResponse($res, ['error' => $e->getMessage()], $e->getCode());
            } catch (\Throwable $e) {
                Controller::logError($e, "GET /v1/customers/deactivate" . $args[0]);
                return Controller::jsonResponse($res, ['error' => Errors::handleErrorCode($e->getCode())], 500);
            }
        };
    }
}
