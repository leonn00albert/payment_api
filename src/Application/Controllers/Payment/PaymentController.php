<?php

declare(strict_types=1);

namespace App\Application\Controllers\Payment;

require_once(__DIR__ . "/../../../../bootstrap.php");

use App\Application\Controllers\Controller;
use App\Application\Controllers\Interfaces\CrudInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Models\Payment;
use App\Utils\Sanitizers\PaymentSanitizer;
use DateTime;
use OpenApi\Annotations as OA;
use Exception;
use PDO;
use Psr\Log\LoggerInterface;
class PaymentController extends Controller implements CrudInterface
{
    public function read(): callable
    {
        return function (Request $req, Response $res): Response {
            try {
                $payment = self::$entityManager->getRepository(Payment::class);
                $data = $payment->findAll();

                $payload =array_map(fn ($pmnt) => (array) $pmnt, $data);
                return Controller::jsonResponse($res,$payload);
            
            } catch (\Throwable $e) {
                Controller::logError($e, "GET /v1/payments");
               Controller::jsonResponse($res,['error' => $e->getMessage()],500);
            }
        };
    }
    public function create(): callable
    {
        return function (Request $req, Response $res): Response {
            try {
                $response = $this->processPaymentRequest($req);
                return  Controller::jsonResponse($res,$response['body'],$response['statusCode']);
            } catch (\Throwable $e) {
                Controller::logError($e, "POST /v1/payments");
                Controller::jsonResponse($res,['error' => $e->getMessage()],500);
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
    
                $paymentId = (int) $args['id'];
                $payment = self::$entityManager->getRepository(Payment::class)->find($paymentId);
    
                if (!$payment) {
                    return Controller::jsonResponse($res, ['error' => 'Payment not found'], 404);
                }
    
                $validatedData = PaymentSanitizer::sanitize($postData, true);
    
                if (!$validatedData) {
                    return Controller::jsonResponse($res, ['error' => 'Invalid input data'], 400);
                }
    
                $payment->setDescription($validatedData['description']);
                $payment->setAmount($validatedData['amount']);
                $payment->setToCustomer($validatedData['recipiant']);
    
                self::$entityManager->flush();
    
                return Controller::jsonResponse($res, ['message' => 'Payment updated successfully'], 200);
            } catch (\Throwable $e) {
                Controller::logError($e, "PUT /v1/payments");
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500)
                    ->withHeader('Content-Type', 'application/json'); // Set the content type header
            }
        };
    }
    
    
    public function delete(): callable
    {
        return function (Request $req, Response $res): Response {
            try {
                $response = $this->processPaymentRequest($req);
                return $this->jsonResponse($res,$response['body'],$response['statusCode']);
            } catch (\Throwable $e) {
                Controller::logError($e);
                Controller::jsonResponse($res,['error' => $e->getMessage()],500);
            }
        };
    }


    private function processPaymentRequest(Request $req, ?bool $update = null): array
    {
        $rawJson = $req->getBody()->getContents();

        if (empty($rawJson)) {
            return ['body' => ['error' => 'Invalid JSON data'], 'statusCode' => 400];
        }

        $postData = json_decode($rawJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['body' => ['error' => 'Invalid JSON format'], 'statusCode' => 400];
        }

        $validatedData = PaymentSanitizer::sanitize($postData, $update);

        if (!$validatedData) {
            return ['body' => ['error' => 'Invalid input data'], 'statusCode' => 400];
        }

        $entityManager = self::$entityManager;
        $payment = new Payment();
        $payment->setDescription($validatedData['description']);
        $payment->setAmount($validatedData['amount']);
        $payment->setCreatedAt(new DateTime());
        $payment->setFromCustomer(000);
        $payment->setToCustomer($validatedData['recipiant']);

        $entityManager->persist($payment);
        $entityManager->flush();

        return ['body' => ['message' => 'Payment added successfully'], 'statusCode' => 201];
    }


}
