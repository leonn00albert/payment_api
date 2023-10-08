<?php

namespace App\Utils\Sanitizers;

class CustomerSanitizer
{
    /**
     * Sanitizes and validates user data for customer registration.
     *
     * @param array $data The user data to be sanitized and validated.
     *
     * @return array|false An array of sanitized and validated data on success, or false on failure.
     */
    public static function sanitize(array $data, ?bool $update = null): array | false
    {
        $validatedData = [];
        if (isset($data['name'])) {
            $validatedData['name'] = htmlspecialchars($data['name']);
        } else {
            if (is_null($update) || $update === false) {
                return false;
            }
        }
        if (isset($data['balance'])) {
            $amount = filter_var($data['balance'], FILTER_VALIDATE_FLOAT);
            if ($amount !== false && $amount >= 0) {
                $validatedData['balance'] = $amount;
            } else {
                return false;
            }
        }
        if (isset($data['email']) && is_string($data['name'])) {
            if (filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
                $validatedData['email'] = htmlspecialchars($data['email']);
            } else {
                return false;
            }
        } 
        if (is_null($update) || $update === false) {
            $requiredFields = ['name', 'email'];
            foreach ($requiredFields as $field) {
                if (!isset($validatedData[$field])) {
                    return false;
                }
            }
        }


        return $validatedData;
    }
}
