<?php

namespace App\Utils\Sanitizers;

class PaymentSanitizer
{
    /**
     * Sanitizes and validates user data for payment registration.
     *
     * @param array $data The user data to be sanitized and validated.
     *
     * @return array|false An array of sanitized and validated data on success, or false on failure.
     */
    public static function sanitize(array $data, ?bool $update = null): array | false
    {
        $validatedData = [];
        if (isset($data['description']) && is_string($data['description'])) {
            $validatedData['description'] = htmlspecialchars($data['description']);
        } else {
            return false;
        }
        if (isset($data['amount'])) {
            $amount = filter_var($data['amount'], FILTER_VALIDATE_FLOAT);
            if ($amount !== false && $amount >= 0) {
                $validatedData['amount'] = $amount;
            } else {
                return false;
            }
        }
        if (isset($data['recipiant'])) {
            $validatedData['recipiant'] = htmlspecialchars($data['recipiant']);
        }
        if(is_null($update)){
            $requiredFields = ['description', 'amount', 'recipiant'];
            foreach ($requiredFields as $field) {
                if (!isset($validatedData[$field])) {
                    return false; 
                }
            }
        }
     

        return $validatedData;
    }


}