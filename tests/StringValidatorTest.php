<?php declare(strict_types=1);
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Core\Validator\StringValidator;

class StringValidatorTest extends TestCase
{
  public function testIsOneOf(): void
  {
    $string_validator = new StringValidator();
    $setup = $string_validator->oneOf(['one', 'string', 'two']);
    $this->assertIsString($setup->validate('string'));

    $this->expectException(Exception::class);
    $setup->validate('nonExisting');
  }
  public function testPassingString(): void
  {
    $string_validator = new StringValidator();
    $is_valid = $string_validator->validate('string');
    $this->assertIsString($is_valid);
  }

  public function testTypeChecking(): void
  {
    $this->expectException(Exception::class);
    $string_validator = new StringValidator();
    $string_validator->checkType()->validate(1);
  }
  public function testRegex(): void
  {
    $string_validator = new StringValidator();
    $setup = $string_validator->regex('/^[a-z]+$/');
    $this->assertIsString($setup->validate('string'));

    $this->expectException(Exception::class);
    $setup->validate('AbAb');
  }
  public function testCasting(): void
  {
    $string_validator = new StringValidator();
    $this->assertSame('1234', $string_validator->validate(1234));
    $this->assertSame('1', $string_validator->validate(true));
    $this->assertSame('', $string_validator->validate(false));
  }
  public function testReturningNullOnNull(): void
  {

    $string_validator = new StringValidator();
    $this->assertSame(null, $string_validator->validate(null));
  }
  public static function acceptingOnlycalarValuesProvider(): array
  {
    return [
      [['string']],
      [new stdClass]
    ];
  }
  #[DataProvider('acceptingOnlycalarValuesProvider')]
  public function testAcceptingScalarValuesOnly($value)
  {
    $this->expectException(Exception::class);
    $string_validator = new StringValidator();
    $string_validator->validate($value);
  }
}