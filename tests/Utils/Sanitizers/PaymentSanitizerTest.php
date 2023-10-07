<?php

use App\Utils\Sanitizers\PaymentSanitizer;
use PHPUnit\Framework\TestCase;
class PaymentSanitizerTest extends TestCase
{
    public function testValidPaymentData()
    {
        $data = [
            'description' => 'Payment for a product',
            'amount' => 100.50,
            'recipiant' => 'John Doe',
        ];

        $result = PaymentSanitizer::sanitize($data);

        $this->assertIsArray($result);
        $this->assertEquals($data, $result);
    }

    public function testInvalidPaymentData()
    {
        $data = [
            'description' => 'Payment for a product',
            'recipiant' => 'John Doe',
        ];

        $result = PaymentSanitizer::sanitize($data);

        $this->assertFalse($result);

        $data = [
            'description' => 'Payment for a product',
            'amount' => 'invalid_amount',
            'recipiant' => 'John Doe',
        ];

        $result = PaymentSanitizer::sanitize($data);

        $this->assertFalse($result);
    }
}
