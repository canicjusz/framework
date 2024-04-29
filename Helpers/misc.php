<?php
require 'debuggers.php';

function local_href($url)
{
  return $_ENV['BASE_DIR'] . '/' . $url;
}

function get_static($path)
{
  return $_ENV['BASE_DIR'] . '/' . $_ENV['STATIC_PATH'] . '/' . $path;
}

function redirect($url, $statusCode = 303)
{
  header('Location: ' . $url, true, $statusCode);
  die();
}
