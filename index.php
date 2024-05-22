<?php

$is_content_rendered = false;

require 'Helpers/misc.php';
require 'Core/autoloader.php';
require 'env.php';
require 'routes.php';
require 'middlewares.php';

use Core\{Route, Database};

Database::createInstance($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

Route::resolve($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
