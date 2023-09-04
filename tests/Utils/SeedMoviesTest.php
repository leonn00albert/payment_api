<?php

declare(strict_types=1);

namespace Tests\Utils;

use App\Utils\SeedMovies;
use Tests\TestCase;

class SeedMoviesTest extends TestCase
{
    public function testApiCallTMDBDiscover()
    {
        $seed = new SeedMovies();
        $result = $seed->apiCallTMDBDiscover();
        $this->assertEquals(20, count($result));
    }
    public function testApiCallTMDBDetail()
    {
        $seed = new SeedMovies();
        $result = $seed->apiCallTMDBDetail(537996);
        $this->assertEquals("The Ballad of Buster Scruggs", $result->title);
    }
    public function testApiCallTMDBCast()
    {
        $seed = new SeedMovies();
        $result = $seed->apiCallTMDBCast(537996);
        $this->assertEquals("Joel Coen", $result->director);
    }
}
