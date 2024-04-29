<?php

function dd(mixed ...$data): void
{
  echo '<pre>';
  foreach ($data as $piece_of_data) {
    var_dump($piece_of_data);
  }
  echo '</pre>';
  echo '<br>';
  die;
}

$dwd_storage = [];

function dwd(mixed ...$data): void
{
  global $is_content_rendered, $dwd_storage;
  array_push($dwd_storage, ...$data);
  if ($is_content_rendered) {
    dwd_resolve();
  }
}

function dwd_resolve()
{
  global $dwd_storage;
  echo '<pre>';
  foreach ($dwd_storage as $piece_of_data) {
    var_dump($piece_of_data);
  }
  echo '</pre>';
  echo '<br>';
}
