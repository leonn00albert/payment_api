<?php

declare(strict_types=1);

namespace App\Application\Controllers\Movie;
use PDO;
use App\Application\Models\Movie;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class MovieConroller 
{
    protected static function index(Request $req,Response $res, PDO $db)
    {
        $movies = Movie::all($db);
        $res->withJson($movies);
    }


}
