<?php

declare(strict_types=1);

namespace App\Application\Actions\Movie;

use App\Application\Models\Movie;
use Psr\Http\Message\ResponseInterface as Response;

class MovieIndexAction extends MovieAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $movies = $this->movieModel->all();
        $this->logger->info("Users list was viewed.");

        return $this->respondWithData($movies);
    }
}
