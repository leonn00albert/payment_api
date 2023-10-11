<?php

declare(strict_types=1);

namespace App\Application\Exceptions;

use Exception;

class CustomerNotFoundException extends Exception
{
    public function __construct($message = 'Customer not found', $code = 404, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
