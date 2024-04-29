<?php

namespace Core;

class Request
{
  public $parameters;
  public $path;
  public $method;
  public $data;
  public $misc;

  public function __construct(string $path, string $method, array $parameters = [])
  {
    global ${'_' . $method};
    $this->data = self::splitQueryVariables(${'_' . $method});
    $this->path = $path;
    $this->method = $method;
    $this->parameters = $parameters;
  }

  // static private function optionalParameters($value, $parameter) {
  //   return str_ends_with($value, '?}');
  // }


  static public function splitQueryVariables(array $queryVariables)
  {
    array_walk($queryVariables, function (&$value) {
      if (is_array($value) && count($value) === 1) {
        $value = explode(',', $value[0]);
      }
    });
    return $queryVariables;
  }

  public function update_data(string $key, string $value)
  {
    $this->data[$key] = $value;
    return $this->build_url();
  }

  public function set_path(string $value)
  {
    $this->path = $value;
    return $this->build_url();
  }

  public function build_url()
  {
    $url = $_ENV['BASE_DIR'] . $this->path;
    return empty($this->data) ? $url : $url . '?' . http_build_query($this->data);
  }

  // static public function
}
