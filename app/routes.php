<?php

declare(strict_types=1);

use App\Application\Controllers\Auth\AuthController;
use App\Application\Controllers\Customer\CustomerController;
use App\Application\Controllers\Docs\DocsController;
use App\Application\Controllers\Payment\PaymentController;
use App\Utils\SeedMovies;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use OpenApi\Annotations as OA;
use Psr\Log\LoggerInterface;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $paymentController = new PaymentController($app->getContainer()->get('doctrine'),$app->getContainer()->get(LoggerInterface::class));
    $customerController = new CustomerController($app->getContainer()->get('doctrine'),$app->getContainer()->get(LoggerInterface::class));

    $docsController = new DocsController;
    $AuthController = new AuthController;

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });


    $app->group("/v1",function ($group) use ($paymentController, $customerController) {
        $group->get('/payments', $paymentController->read());
        $group->post('/payments', $paymentController->create());

        $group->get('/customers', $customerController->read());
        $group->post('/customers', $customerController->create());
    });

    $app->get('/swagger.json', $docsController->swaggerFile());
    $app->post('/register', $AuthController->register());
    $app->get('/docs', $docsController->index());



};
