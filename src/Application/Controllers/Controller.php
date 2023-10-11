<?php

namespace App\Application\Controllers;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Doctrine\ORM\EntityManager;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Payment API",
 *     version="1.0.0",
 *     description="an api for payments"
 * )
 * Base controller class for common controller functionality.
 */
class Controller
{
    /**
     * @var LoggerInterface The logger instance.
     */
    protected static LoggerInterface $logger;

    /**
     * @var EntityManager The entity manager instance.
     */
    protected static EntityManager $entityManager;

    /**
     * Controller constructor.
     *
     * @param EntityManager $entityManager The entity manager instance.
     * @param LoggerInterface $logger The logger instance.
     */
    public function __construct(EntityManager $entityManager, LoggerInterface $logger)
    {
        self::$entityManager = $entityManager;
        self::$logger = $logger;
    }

    /**
     * Responds with a JSON-encoded response.
     *
     * @param Response $res The response object.
     * @param array $data The data to be JSON-encoded.
     * @param int $status The HTTP response status code (default: 200).
     * @return Response The response with JSON content.
     */
    public static function jsonResponse(Response $res, array $data, int $status = 200): Response
    {
        $res->getBody()->write(json_encode($data));
        return $res->withStatus($status)->withHeader('Content-Type', 'application/json');
    }

    /**
     * Logs an error message.
     *
     * @param \Throwable $e The exception or error to log.
     */
    public static function logError(\Throwable $e, string $endpoint = ""): void
    {
        self::$logger->error("request to: " . $endpoint . " - " . $e->getMessage());
    }
}
