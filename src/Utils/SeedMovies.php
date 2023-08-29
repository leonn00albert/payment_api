<?php
namespace App\Utils;
class SeedMovies {

    function omdbApiCall(int $amount= 20):array
    {
        $apiKey = 'e160d62c';
        $apiUrl = "http://www.omdbapi.com/?apikey=$apiKey&t=" . urlencode($movieTitle);
        $response = file_get_contents($apiUrl);
        return $response;
    }
}