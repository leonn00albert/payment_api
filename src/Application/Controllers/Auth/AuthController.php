<?php

declare(strict_types=1);

namespace App\Application\Controllers\Auth;

use Exception;
use PDO;
use App\Application\Models\User;
use Closure;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

class AuthController
{
    /**
     * @SWG\Get(
     *   path="/v1/register",
     *   tags={"Registration"},
     *   summary="Register a new user",
     *   @SWG\Response(
     *     response=200,
     *     description="Successful registration",
     *     @SWG\Schema(
     *       type="object",
     *       @SWG\Property(property="message", type="string", example="User registered successfully"),
     *       @SWG\Property(property="apiKey", type="string", example="your_api_key")
     *     )
     *   )
     * )
     *
     *  * Register a new user and return API key
     * @return callable The HTTP response with JSON data.
     */


    public function register(): callable
    {
        return (function (Request $req, Response $res) {
            $rawJson = $req->getBody()->getContents();
            if (empty($rawJson)) {
                $res->getBody()->write(json_encode(['error' => 'Invalid JSON data']));
                return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            $postData = json_decode($rawJson, true);
            try {
                $postData["email"] ?? throw new Exception("Please submit an email with your request.");
                $email = filter_var($postData["email"], FILTER_VALIDATE_EMAIL) ? $postData["email"] : throw new Exception("Email is not valid");
                $apiKey = AuthController::generateApiKey();

                User::register($this->get(PDO::class), [
                    "email" => $email,
                    "api_key" => $apiKey
                ]);
                AuthController::addToAllowList($apiKey);
                $res->getBody()->write(json_encode(['message' => 'User registered successfully', 'apiKey' => $apiKey]));
                return $res->withStatus(200)->withHeader('Content-Type', 'application/json');
            } catch (Throwable $e) {
                $res->getBody()->write(json_encode(['error' => $e->getMessage()]));
                return $res->withStatus(200)->withHeader('Content-Type', 'application/json');
            }
        });
    }
    /**
     * Generate a random API key.
     *
     * @param int $length The length of the API key (default is 32 characters).
     *
     * @return string The generated API key.
     */
    static function generateApiKey($length = 32): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $apiKey = '';

        for ($i = 0; $i < $length; $i++) {
            $apiKey .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $apiKey;
    }

    /**
     * Adds an API key to the allow list and stores it in a JSON file.
     *
     * @param string $api_key The API key to add to the allow list.
     *
     * @throws Exception If there is an error creating, reading, or updating the JSON file.
     *
     * @return string A message indicating whether the API key was added or already exists in the allow list.
     */
    static function addToAllowList(string $api_key): string
    {
        $jsonFilePath = './api_keys.json';

        try {
            if (!file_exists($jsonFilePath)) {
                $data = ["allowed_api_keys" => []];
                $jsonContents = json_encode($data);

                if (file_put_contents($jsonFilePath, $jsonContents) === false) {
                    throw new Exception('Failed to create the JSON file.');
                }
            }

            $jsonContents = file_get_contents($jsonFilePath);

            if ($jsonContents === false) {
                throw new Exception('Failed to read the JSON file.');
            }

            $data = json_decode($jsonContents, true);

            if ($data === null) {
                throw new Exception('Failed to decode the JSON data.');
            }

            if (!in_array($api_key, $data['allowed_api_keys'])) {
                $data['allowed_api_keys'][] = $api_key;

                $jsonContents = json_encode($data);

                if (file_put_contents($jsonFilePath, $jsonContents) === false) {
                    throw new Exception('Failed to update the JSON file.');
                }
                return 'API key added to the allow list.';
            } else {
                return 'API key already in the allow list.';
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    /**
     * Check if an API key is allowed based on the allow list stored in a JSON file.
     *
     * @param string $api_key The API key to check.
     *
     * @throws Exception If there is an error reading or decoding the JSON file.
     *
     * @return bool True if the API key is allowed, false otherwise.
     */
    static function checkIfKeyIsAllowed(string $api_key): bool
    {
        $jsonFilePath = './api_keys.json';

        try {
            if (!file_exists($jsonFilePath)) {
                throw new Exception('JSON file does not exist.');
            }

            $jsonContents = file_get_contents($jsonFilePath);

            if ($jsonContents === false) {
                throw new Exception('Failed to read the JSON file.');
            }

            $data = json_decode($jsonContents, true);

            if ($data === null) {
                throw new Exception('Failed to decode the JSON data.');
            }

            return in_array($api_key, $data['allowed_api_keys']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
