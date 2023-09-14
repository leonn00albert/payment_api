<?php

namespace App\Utils;

use App\Application\Models\Movie;
use Illuminate\Database\Capsule\Manager as Capsule;
use stdClass;

require __DIR__ . '/../../vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();
class SeedMovies
{
    private array $movie_data = [];
    public function apiCallTMDBDiscover(): array
    {
        $client = new \GuzzleHttp\Client();
        $page_number = rand(0, 500);
        $response = $client->request('GET', 'https://api.themoviedb.org/3/discover/movie?include_adult=false&include_video=false&language=en-US&page=' . $page_number . '&sort_by=popularity.desc', [
          'headers' => [
            'Authorization' => 'Bearer ' . $_ENV["TMDB_ACCESS_TOKEN"],
            'accept' => 'application/json',
          ],
        ]);

        return json_decode((string) $response->getBody())->results;
    }

    public function apiCallTMDBDetail(int $movie_id): object
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', sprintf('https://api.themoviedb.org/3/movie/%s?language=en-US', $movie_id), [
          'headers' => [
            'Authorization' => 'Bearer ' . $_ENV["TMDB_ACCESS_TOKEN"],
            'accept' => 'application/json',
          ],
        ]);

        return json_decode((string) $response->getBody());
    }

    public function apiCallTMDBCast(int $movie_id): object
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', sprintf('https://api.themoviedb.org/3/movie/%s/credits?language=en-US', $movie_id), [
          'headers' => [
            'Authorization' => 'Bearer ' . $_ENV["TMDB_ACCESS_TOKEN"],
            'accept' => 'application/json',
          ],
        ]);
        $credits = json_decode((string) $response->getBody());
        return (object)[
          "actors" => (function () use ($credits) {
            $actors = array_map(fn($actor) => $actor->name, array_slice($credits->cast, 0, 5));
             return implode(", ", $actors);
          })(),
          "director" => (function () use ($credits) {
            foreach ($credits->crew as $crewMember) {
                if ($crewMember->department == "Directing" && $crewMember->job == "Director") {
                    return $crewMember->name;
                }
            }
          })()
        ];
    }
    public function seed(int $amount = 10): array
    {
        $result = [];
        $this->movie_data = $this->apiCallTMDBDiscover();

        foreach (range(0, $amount) as $i) {
            $data = $this->apiCallTMDBDetail($this->movie_data[$i]->id);
            $cast = $this->apiCallTMDBCast($this->movie_data[$i]->id);
            $movie = new stdClass();
            $movie->uid = $data->id;
            $movie->title = $data->title;
            $movie->year = explode("-", $data->release_date)[0];
            $movie->released = $data->release_date;
            $movie->runtime = $data->runtime . ' min';
            $movie->overview = $data->overview;
            $movie->genre = $data->genre_ids[0]->name ?? "";
            $movie->director = $cast->director;
            $movie->actors = $cast->actors;
            $movie->country = $data->production_countries[0]->name ?? "";
            $movie->poster = $data->poster_path;
            $movie->imdb_id = $data->imdb_id;
            $movie->imdb = $data->vote_average;
            $movie->type = "movie";
            $result[] = $movie;
        }
        return $result;
    }
}
