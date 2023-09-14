<?php

declare(strict_types=1);

use App\Application\Controllers\Auth\AuthController;
use App\Application\Middleware\SessionMiddleware;
use Psr\Log\LoggerInterface;
use Slim\App;

return function (App $app) {
    $LoggerMiddleware = function ($request, $handler) {
        $logger = $this->get(LoggerInterface::class);
        $routeContext = $request->getAttribute(\Slim\Routing\RouteContext::class);
        $route = $routeContext ? $routeContext->getRoute()->getPattern() : 'Unknown Route';
        $method = $request->getMethod();
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? 'Unknown IP';

        $response = $handler->handle($request);
        $headers = $request->getHeaders();
        foreach ($headers as $name => $values) {
            
            $logger->info($name . ": " . implode(", ", $values));
            $logger->info("Route: $route, Method: $method, IP: $ip");
        }


        return $response;
    };

    $AuthMiddleware = function ($request, $handler) {
        if($_ENV["environment"] !== 'test'){
            $apiKey = $request->getHeaderLine('api_key');
        
            if (isset($apiKey))
                if (AuthController::checkIfKeyIsAllowed($apiKey)) {
                    return $handler->handle($request);
                }
            
            $response = new \Slim\Psr7\Response();
            $response = $response->withStatus(401);
            $response->getBody()->write("Unauthorized! register your email at v1/register");
            return $response;
        }
 
    };
    $app->add($AuthMiddleware);
    $app->add(SessionMiddleware::class);
    $app->add($LoggerMiddleware);
};
