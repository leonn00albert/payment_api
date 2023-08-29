<?php

declare(strict_types=1);

namespace Tests\Utils;

use App\Utils\SeedMovies;
use Tests\TestCase;

class SeedMoviesTest extends TestCase
{
    public function test()
    {
        $seed = new SeedMovies();
        $result = $seed->omdbApiCall();
        $this->assertEquals(20, count($result));
    }
}
