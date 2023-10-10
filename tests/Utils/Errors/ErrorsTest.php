<?php
use PHPUnit\Framework\TestCase;
use App\Utils\Errors\Errors;

class ErrorsTest extends TestCase
{
    public function testHandleErrorCode()
    {
        $this->assertEquals("Email already taken", Errors::handleErrorCode(1062));

        $this->assertEquals("Access denied for user", Errors::handleErrorCode(1045));

        $this->assertEquals("Unknown error", Errors::handleErrorCode(9999));
    }
}