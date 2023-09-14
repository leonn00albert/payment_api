<?php

declare(strict_types=1);

namespace App\Application\Controllers\Docs;

use Closure;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class DocsController
{
    public function index(): Closure
    {
        return function (Request $req, Response $res) {
            $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Swagger UI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.6.2/swagger-ui.min.css" integrity="sha512-wjyFPe3jl9Y/d+vaEDd04b2+wzgLdgKPVoy9m1FYNpJSMHM328G50WPU57xayVkZwxWi45vA+4QN+9erPZIeig==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/png" href="favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="favicon-16x16.png" sizes="16x16" />
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }

        *,
        *:before,
        *:after {
            box-sizing: inherit;
        }

        body {
            margin: 0;
            background: #fafafa;
        }
    </style>
</head>

<body>
<div id="swagger-ui"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/3.12.1/swagger-ui-bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/3.12.1/swagger-ui-standalone-preset.js"></script>
<script>
    window.onload = function () {
        console.log(window.location.pathname);
        const ui = SwaggerUIBundle({
            url: window.location.protocol + "//" + window.location.hostname + ":" + window.location.port + "/swagger.json",            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            layout: "StandaloneLayout"
        });
        window.ui = ui;
    };
</script>
</body>
</html>
HTML;

            $res->getBody()->write($html);
            return $res->withHeader('Content-Type', 'text/html');
        };
    }

    public function swaggerFile(): closure
    {
        return (function (Request $req, Response $res) {
            $swaggerJsonPath = __DIR__ . '/../../../../swagger.json';
            $res->getBody()->write(file_get_contents($swaggerJsonPath));
            return $res->withHeader('Content-Type', 'application/json');
        });
    }
}
