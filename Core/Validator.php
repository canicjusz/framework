<?php

namespace Core;

use Core\Validator\{SchemaValidator, StringValidator, IntValidator};

class Validator
{
  public function __construct($base)
  {

  }
  public static function int()
  {
    return new IntValidator();
  }
  public static function string()
  {
    return new StringValidator();
  }
  public static function schema(array $val)
  {
    return new SchemaValidator($val);
  }
}