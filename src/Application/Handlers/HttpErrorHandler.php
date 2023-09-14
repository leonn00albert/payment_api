<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;

class HttpErrorHandler extends SlimErrorHandler
{
    /**
     * @inheritdoc
     */
    protected function respond(): Response
    {
        $exception = $this->exception;
        $statusCode = 500;
        $errorMessage = 'An internal error has occurred while processing your request.';

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
            $errorMessage = $exception->getMessage();

            if ($statusCode == 405) {
                $statusCode = 404;
                $errorMessage = "page no found!";
            }
        }

        $errorResponse = [
            'status' => 'error',
            'code' => $statusCode,
            'message' => $errorMessage,
        ];

        $encodedResponse = json_encode($errorResponse, JSON_PRETTY_PRINT);

        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($encodedResponse);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
