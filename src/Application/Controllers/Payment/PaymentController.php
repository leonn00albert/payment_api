<?php

declare(strict_types=1);

namespace App\Application\Controllers\Payment;

use App\Application\Controllers\Controller;
use App\Application\Controllers\Interfaces\CrudInterface;
use App\Application\Exceptions\PaymentNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Models\Payment;
use App\Utils\Sanitizers\PaymentSanitizer;
use DateTime;
use OpenApi\Annotations as OA;



class PaymentController extends Controller implements CrudInterface
{
    /**
     * @OA\Get(
     *     path="/v1/payments",
     *     summary="Retrieve a list of payments",
     *     tags={"Payments"},
     *     @OA\Response(
     *         response=200,
     *         description="List of payments",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Payment")
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
                $payment = self::$entityManager->getRepository(Payment::class);
                $data = $payment->findAll();

                $payload = array_map(fn (Payment $pmnt) => $pmnt->toArray(), $data);
                return Controller::jsonResponse($res, $payload);
            } catch (\Throwable $e) {
                Controller::logError($e, "GET /v1/payments");
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500);
            }
        };
    }
    /**
     * @OA\Post(
     *     path="/v1/payments",
     *     summary="Create a new payment",
     *     tags={"Payments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment added successfully",
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
                $response = $this->processPaymentRequest($req);
                return  Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (\Throwable $e) {
                Controller::logError($e, "POST /v1/payments");
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500);
            }
        };
    }
    /**
     * @OA\Put(
     *     path="/v1/payments/{paymentId}",
     *     summary="Update a payment",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="paymentId",
     *         in="path",
     *         description="ID of the payment to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment updated successfully",
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
     *         description="Payment not found",
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

                $paymentId = (int) $args[0];
                $payment = self::$entityManager->getRepository(Payment::class)->find($paymentId);

                if (!$payment) {
                    throw new PaymentNotFoundException();
                }

                $validatedData = PaymentSanitizer::sanitize($postData, true);

                if (!$validatedData) {
                    return Controller::jsonResponse($res, ['error' => 'Invalid input data'], 400);
                }
                if (isset($validatedData['description'])) {
                    $payment->setDescription($validatedData['description']);
                }
                if (isset($validatedData['recipiant'])) {
                    $payment->setToCustomer($validatedData['recipiant']);
                }
                if (isset($validatedData['amount'])) {
                    $payment->setAmount($validatedData['amount']);
                }


                self::$entityManager->flush();

                return Controller::jsonResponse($res, ['message' => 'Payment updated successfully'], 200);
            } catch (PaymentNotFoundException $e) {
                return  Controller::jsonResponse($res, ['error' => $e->getMessage()], $e->getCode());
            } catch (\Throwable $e) {
                Controller::logError($e, "PUT /v1/payments");
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500)
                    ->withHeader('Content-Type', 'application/json'); // Set the content type header
            }
        };
    }

    /**
     * @OA\Delete(
     *     path="/v1/payments/{paymentId}",
     *     summary="Delete a payment",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="paymentId",
     *         in="path",
     *         description="ID of the payment to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found",
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
                    $payment = PaymentController::$entityManager->find(Payment::class, $args[0]);
                }
                if (isset($payment)) {
                    PaymentController::$entityManager->remove($payment);
                    PaymentController::$entityManager->flush();
                    $response = ['body' => ['message' => 'Payment deleted successfully.'], 'statusCode' => 200];
                } else {
                    throw new PaymentNotFoundException();
                }
                return Controller::jsonResponse($res, $response['body'], $response['statusCode']);
            } catch (PaymentNotFoundException $e) {
                return  Controller::jsonResponse($res, ['error' => $e->getMessage()], $e->getCode());
            } catch (\Throwable $e) {
                Controller::logError($e, "DELETE /v1/payments/" . $args[0]);
                return Controller::jsonResponse($res, ['error' => $e->getMessage()], 500);
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
