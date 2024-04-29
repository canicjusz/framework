<?php

namespace Core;

class callbackValidator
{
  public static function validate(object|array $callback, string $exception_message)
  {
    if (!is_callable($callback)) {
      if (!is_array($callback)) {
        throw new \Exception($exception_message);
      }
      if (count($callback) != 2) {
        throw new \Exception($exception_message);
      }
      $class_name = $callback[0];
      $method = $callback[1];

      $class = new $class_name();
      if (!method_exists($class, $method)) {
        throw new \Exception("'$method' doesn't exist on the $class class.");
      }
      $callback_array = [$class, $method];
    }
    $final_callback = $callback_array ?? $callback;
    if (!is_callable($final_callback)) {
      throw new \Exception($exception_message);
    }
    return $final_callback;
  }
}
