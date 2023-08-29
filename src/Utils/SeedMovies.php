<?php
namespace App\Utils;
require __DIR__ . '/../../vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();
class SeedMovies {
  private array $movie_data = [];
    function omdbApiCall()
    {
        $client = new \GuzzleHttp\Client();
        $page_number = rand(0,500);
        $response = $client->request('GET', 'https://api.themoviedb.org/3/discover/movie?include_adult=false&include_video=false&language=en-US&page='. $page_number. '&sort_by=popularity.desc', [
          'headers' => [
            'Authorization' => 'Bearer ' . $_ENV["TMDB_ACCESS_TOKEN"],
            'accept' => 'application/json',
          ],
        ]);
        
        return json_decode((string) $response->getBody())->results;
       
    }
    public function seed(int $amount= 20):array {
        $data = [];
        foreach(range(0,$amount) as $i){
            $data[] = $this->omdbApiCall();
        }
        return $data;
    }


}