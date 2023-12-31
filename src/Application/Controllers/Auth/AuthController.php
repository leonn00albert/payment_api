<?php

declare(strict_types=1);

namespace App\Application\Controllers\Auth;

use Exception;
use PDO;
use OpenApi\Annotations as OA;
use Closure;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

/**
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="JWT",
 *     name="jwt_token",
 *     description="JWT Token for authentication"
 * )
 */
class AuthController
{
/**
 * @OA\Post(
 *     path="/register",
 *     summary="Register a user",
 *     description="Registers a user and returns an API key.",
 *     operationId="register",
 *     tags={"User"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(property="email", type="string", format="email", example="user@example.com")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User registered successfully"),
 *             @OA\Property(property="apiKey", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid JSON data",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Invalid JSON data")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Please submit a valid email.")
 *         )
 *     )
 * )
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
    public static function generateApiKey($length = 32): string
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
    public static function addToAllowList(string $api_key): string
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
    public static function checkIfKeyIsAllowed(string $api_key): bool
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
