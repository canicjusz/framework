<?php

namespace Core\Validator;

use Core\Contracts\PrimitiveValidatorInterface;

class SchemaValidator
{

  private array $schema;
  public function __construct(array $schema)
  {
    foreach ($schema as $property_name => $schema_unit) {
      if (!($schema_unit instanceof PrimitiveValidatorInterface)) {
        throw new \Exception("Value of '$property_name' passed as a schema element isn't a validator.");
      }
    }
    $this->schema = $schema;
  }

  public function validate(array $data): ?array
  {
    $schema_data_diff = array_diff_key($data, $this->schema);

    foreach ($schema_data_diff as $property_name => $value) {
      if (!isset($this->schema[$property_name])) {
        throw new \Exception("Field '$property_name' doesn't exist on the schema: {$this}");
      }
    }
    $casted_data = [];
    foreach ($this->schema as $property_name => $schema_validator) {
      $casted_value = $schema_validator->validate($data[$property_name] ?? null, $property_name);
      if (isset($casted_value))
        $casted_data[$property_name] = $casted_value;
    }
    return $casted_data;
  }

  public function __toString()
  {
    $key_value_pairs = array_map(fn($property_name, $class) => $property_name . " => " . $class::class, array_keys($this->schema), array_values($this->schema));
    $joined_key_value_pairs = join(', ', $key_value_pairs);
    return "[$joined_key_value_pairs]";
  }
}