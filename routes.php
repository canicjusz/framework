<?php

use Core\{Route, ErrorRoute};
use Controllers\Post;

Route::get(
  '/',
  function () {
    echo 'Hello World!';
  }
);

Route::get(
  '/post/{id}',
  [Post::class, 'index'],
  ['id' => '[0-9]+']
);

ErrorRoute::add(
  ErrorRoute::CODE_MAP['NOT_FOUND'],
  function () {
    echo 'File not found!';
  }
);
