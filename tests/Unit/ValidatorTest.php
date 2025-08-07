<?php

namespace Tests\Unit;

use Core\Validator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ValidatorTest extends TestCase
{
    #[Test]
    public function required_field()
    {
        // Arrange: Set up the data
        $data = ['name' => ''];
        $validator = new Validator($data);

        // Act: Run the validation
        $validator->validate(['name' => ['required']]);

        // Assert: Check the outcome
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->getErrors());
    }

    #[Test]
    public function valid_email()
    {
        // Arrange
        $data = ['email' => 'test@example.com'];
        $validator = new Validator($data);

        // Act
        $validator->validate(['email' => ['email']]);

        // Assert
        $this->assertTrue(!$validator->fails());
    }

    #[Test]
    public function invalid_email()
    {

        // Arrange
        $data = ['email' => 'not-an-email'];
        $validator = new Validator($data);

        // Act
        $validator->validate(['email' => ['email']]);

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->getErrors());
    }

    #[Test]
    public function min_length()
    {

        // Arrange
        $data = ['password' => '123456'];
        $validator = new Validator($data);

        // Act
        $validator->validate(['password' => ['min:8']]);

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->getErrors());
    }
}
