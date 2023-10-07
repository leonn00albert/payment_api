<?php

declare(strict_types=1);

namespace App\Application\Controllers\Payment;
require_once (__DIR__ . "/../../../../bootstrap.php");
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Models\Movie;
use App\Application\Models\Payment;
use DateTime;
use OpenApi\Annotations as OA;
use Exception;
use PDO;
use Psr\Log\LoggerInterface;

class PaymentController
{
    protected static $logger;

    public function __construct($logger)
    {
        self::$logger = $logger;
    }

    public function index(): callable
    {
        return function (Request $req, Response $res): Response {
            try {
                $entityManager = $this->get('doctrine');
                $payment = $entityManager->getRepository(Payment::class);
                $data = $payment->findAll();
                $payment = new Payment();
                $payment->setDescription("daasdas");
                $payment->setAmount(2000);
                $payment->setCreatedAt(new DateTime());
                $payment->setFromCustomer(1);
                $payment->setToCustomer(2);
            
                // Persist the Payment entity to the database
                $entityManager->persist($payment);
                $entityManager->flush(); // Save changes to the database
            
                $payload = json_encode($data);
                $res->getBody()->write($payload);

                return $res->withHeader('Content-Type', 'application/json');
            } catch (\Throwable $e) {
                PaymentController::$logger->error("request to /v1/payments " . $e->getMessage());
                $res->getBody()->write(json_encode(['error' => $e->getMessage()]));
                return $res->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        };
    }

 
}
