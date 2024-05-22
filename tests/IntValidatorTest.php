<?php declare(strict_types=1);
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Core\Validator\IntValidator;

class IntValidatorTest extends TestCase
{
  public function testIsOneOf(): void
  {
    $int_validator = new IntValidator();
    $setup = $int_validator->oneOf([1, 22, 333]);
    $this->assertIsInt($setup->validate(22));

    $this->expectException(Exception::class);
    $setup->validate(444);
  }
  public function testPassingString(): void
  {
    $int_validator = new IntValidator();
    $is_valid = $int_validator->validate(123123);
    $this->assertIsInt($is_valid);
  }

  public function testTypeChecking(): void
  {
    $this->expectException(Exception::class);
    $int_validator = new IntValidator();
    $int_validator->checkType()->validate('NaN');
  }
  public function testRegex(): void
  {
    $int_validator = new IntValidator();
    $setup = $int_validator->regex('/^[0-9]{3}$/');
    $this->assertIsInt($setup->validate(111));

    $this->expectException(Exception::class);
    $setup->validate(4444);
  }
  public function testCasting(): void
  {
    $int_validator = new IntValidator();
    $this->assertSame(1234, $int_validator->validate('1234'));
    $this->assertSame(1, $int_validator->validate(true));
    $this->assertSame(0, $int_validator->validate(false));
  }
  public function testReturningNullOnNull(): void
  {
    $int_validator = new IntValidator();
    $this->assertSame(null, $int_validator->validate(null));
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
    $int_validator = new IntValidator();
    $int_validator->validate($value);
  }
}