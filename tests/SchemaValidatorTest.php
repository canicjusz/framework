<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Core\Validator\SchemaValidator;
use Core\Validator;

class SchemaValidatorTest extends TestCase
{
  public static function passingValidSchemaProvider(): array
  {
    return [
      [
        [
          'name' => Validator::string(),
          'age' => Validator::int()
        ]
      ],
    ];
  }

  #[DataProvider('passingValidSchemaProvider')]
  public function testPassingValidSchema(array $schema): void
  {
    $this->expectNotToPerformAssertions();
    new SchemaValidator($schema);
  }

  public static function passingInvalidSchemaProvider(): array
  {
    return [
      [
        [
          'name' => 'a',
          'age' => Validator::int()
        ],
        [
          'name' => Validator::string(),
          'age' => 9
        ],
      ]
    ];
  }
  #[DataProvider('passingInvalidSchemaProvider')]
  public function testPassingInvalidSchema(array $schema): void
  {
    $this->expectException(Exception::class);
    new SchemaValidator($schema);
  }

  public function testValidatingSelectiveData(): void
  {
    $unstrict_schema = Validator::schema([
      'name' => Validator::string(),
      'age' => Validator::int()
    ]);

    $selective_data = ['name' => 'jan'];
    $validated_data = $unstrict_schema->validate($selective_data);
    $this->assertSame(['name' => 'jan'], $validated_data);

    $data_with_unset_field = ['name' => 'jan', 'age' => null];
    $validated_data = $unstrict_schema->validate($data_with_unset_field);
    $this->assertSame(['name' => 'jan'], $validated_data);

    $strict_schema = Validator::schema([
      'name' => Validator::string()->required(),
      'age' => Validator::int()->required()
    ]);
    $this->expectException(Exception::class);
    $strict_schema->validate($selective_data);
  }

  public function testPassingSurplusData(): void
  {
    $surplus_data = [
      'name' => 'jan',
      'age' => 1
    ];
    $insufficient_schema = Validator::schema([
      'age' => Validator::int()
    ]);
    $this->expectException(Exception::class);
    $insufficient_schema->validate($surplus_data);
  }

  public static function castingTestProvider(): array
  {
    return [
      [
        [
          'age' => Validator::int()
        ],
        [
          'age' => '1'
        ],
        [
          'age' => 1
        ],
      ],
      [
        [
          'age' => Validator::string()
        ],
        [
          'age' => 1
        ],
        [
          'age' => '1'
        ],
      ],
    ];
  }
  #[DataProvider('castingTestProvider')]
  public function testCasting(array $schema, array $data, array $expected_data): void
  {
    $schema_validator = new SchemaValidator($schema);
    $casted_data = $schema_validator->validate($data);
    $this->assertSame($expected_data, $casted_data);
  }
}