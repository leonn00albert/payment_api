<?php

use PHPUnit\Framework\TestCase;
use App\Utils\Sanitizers\CustomerSanitizer;

class CustomerSanitizerTest extends TestCase
{
    public function testValidInput()
    {
        $data = [
            'name' => 'John Doe',
            'balance' => 100.50,
            'email' => 'john@example.com',
        ];

        $result = CustomerSanitizer::sanitize($data);

        $this->assertIsArray($result);
        $this->assertEquals('John Doe', $result['name']);
        $this->assertEquals(100.50, $result['balance']);
        $this->assertEquals('john@example.com', $result['email']);
    }

    public function testInvalidInput()
    {
        $data = [
            'balance' => 100.50,
            'email' => 'john@example.com',
        ];

        $result = CustomerSanitizer::sanitize($data);

        $this->assertFalse($result);

        $data = [
            'name' => 'John Doe',
            'balance' => 100.50,
            'email' => 'invalid_email',
        ];

        $result = CustomerSanitizer::sanitize($data);

        $this->assertFalse($result);
    }

    public function testRequiredFieldsForUpdate()
    {
        $data = [
            'balance' => 100.50,
        ];

        $result = CustomerSanitizer::sanitize($data, null);

        $this->assertFalse($result);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $result = CustomerSanitizer::sanitize($data, null);

        $this->assertIsArray($result);
    }
}
