<?php declare(strict_types=1);

use PHPUnit\Framework\{TestCase};
use PHPUnit\Framework\Attributes\DataProvider;
use Core\Validator;
use Core\Validator\{StringValidator, IntValidator, SchemaValidator};

class ValidatorTest extends TestCase
{
  public static function returnTestProvider(): array
  {
    return [
      ['string', StringValidator::class],
      ['int', IntValidator::class],
      ['schema', SchemaValidator::class, []]
    ];
  }

  #[DataProvider('returnTestProvider')]
  public function testReturnedInstance(string $func, string $instance, ...$args): void
  {
    $typeValidator = call_user_func_array([Validator::class, $func], $args);
    $this->assertInstanceOf($instance, $typeValidator);
  }
}