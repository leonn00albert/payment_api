<?php

declare(strict_types=1);

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
    $logger->info("Route: $route, Method: $method, IP: $ip");
    $logger->info($name . ": " . implode(", ", $values));
    
}


        return $response;
    };
    $app->add(SessionMiddleware::class);
    $app->add($LoggerMiddleware);
};
