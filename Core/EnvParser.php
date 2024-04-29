<?php

namespace Core;

class EnvParser
{
  static public function parse(string $filename)
  {
    $env = parse_ini_file(ROOT_PATH . DIRECTORY_SEPARATOR . $filename);
    $default_path_variables = ['BASE_DIR', 'CSS_PATH', 'JS_PATH', 'PHOTO_PATH', 'STATIC_PATH'];
    $_ENV['BASE_DIR'] = '';
    $_ENV['CSS_PATH'] = 'public/css';
    $_ENV['JS_PATH'] = 'public/js';
    $_ENV['STATIC_PATH'] = 'public/static';
    foreach ($env as $env_variable => $env_value) {
      if (in_array($env_variable, $default_path_variables) && substr($env_value, -1) === '/') {
        $env_value = substr($env_value, 0, -1);
      }
      $_ENV[$env_variable] = $env_value;
    }
  }
}
