<?php

declare(strict_types=1);

namespace App\Application\Controllers\Payment;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Models\Movie;
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
                $cachedData = false;
                if (method_exists($this, 'get')) {
                    $memcache = $this->get('memcache');
                    $cachedData = $memcache->get('payment_index');
                }

                if ($cachedData === false) {
                    $data = Payments::all();
                    if (isset($memcache)) {
                        $memcache->set('payment_index', $data, 3600);
                    }
                } else {
                    $data = $cachedData;
                }
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
