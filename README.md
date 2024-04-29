# Framework

This is a framework created for my personal use. Nothing too fancy, it's simply a result of me dabbling with PHP in my spare time. The syntax was inspired by Laravel.

Features:

- [a router for dynamic and static routes](#router)
- [a MySQL ORM (supports only SELECT statement and CTE for now)](#orm)
- utilizes the MVC design pattern with middlewares
- [head element is accessible and modifiable in every component of the page](#Views)
- [environment variables support](#environment-variables)

## File structure

- `Core` - the backbone of the framework
- [`Helpers`](#additional-helper-functions) - contains helper functions automatically loaded at the beginning of `index.php`.

  [`└ debuggers.php`](#debugging-tools) - debugging functions

  `└ misc.php` - miscellaneous helper functions

- [`Controllers`](#controllers) - contains controllers
- [`Models`](#models) - contains models
- [`Views`](#views) - contains views

  [`└ Partials`](#partials) - contains partial views

- [`Middlewares`](#middlewares) - contains middlewares
- `public`

  `└ css` - contains css files

  `└ js` - contains js files

  `└ static` - contains static files

- [`env.php`](#environment-variables) - a configuration file for environmental variables
- [`middlewares.php`](#middlewares) - a file for registering middlewares
- [`routes.php`](#router) - a file for registering routes
- `index.php` - the initializing file
- `.htaccess` - redirects all requests to `index.php`.

## Environment Variables

The default file for environment variables is `.env`. You can change the configuration in `env.php`.

**Required** env variables:

- `DB_HOST`
- `DB_USER`
- `DB_PASSWORD`
- `DB_NAME`

Additional env variables:

- `BASE_DIR` - the directory in which the app lives on the server (default: `/`)
- `CSS_PATH` - the path to css files (default: `public/css`)
- `JS_PATH` - the path to js files (default: `public/js`)
- `STATIC_PATH` - the path to static files (default: `public/static`)

## Router

Inside of the `routes.php` file you can define routes, just like the name suggests. Here is an example:

```
use Core\Route;
use Controllers\Catalog;

Route::get(
	'/',
	function () {
		echo 'Hello World!';
	}
);

// would match: category/123/13
// would NOT match: category/asd24
Route::get(
	'/posts/{id[]}',
	[Catalog::class, 'index'],
	['id' => '[0-9]+']
);
```

The `Route` class has corresponding CRUD methods: `get`, `post`, `delete`, `put` (and in addition `patch`). The parameters of these methods are as follows:

- `path` - as the name implies
- `callback` - a function or an array with a class and method's name
- `parameter_constraints` - an associative array describing the regex pattern of parameters that has to be met

Available parameter types:

- one obligatory parameter `{param}`
- one optional parameter `{param?}
- multiple obligatory (at least one) parameters `{param[]}`
- multiple optional parameters `{param[]?}`

To add an error route use the `ErrorRoute` class like so:

```
ErrorRoute::add(
	 ErrorRoute::CODE_MAP['NOT_FOUND'],
	 [View::open('404.php'), 'load']
);
```

`ErrorRoute::add` parameters:

- `code` - code of the error, you can use the additional CODE_MAP enum to improve code readability
- `callback` - a function or an array with a class and method's name

## Middlewares

Are stored in the `Middlewares` directory. Example file:

```
namespace Middlewares;
class showRequest {
	function handle($request){
	    dwd('Here is the request: ', $request);
	}
}
```

Now, you have to register the middleware in `middlewares.php`:

```
use Core\Middleware;
use Middlewares\showRequest;

// the first argument is middleware's alias, the second one is a reference to the method
Middleware::add(
	'showRequest',
	[showRequest::class, 'handle']
);
```

And you can finally add the middleware to a route like so:

```
Route::get(
  '/',
  [Home::class, 'index'],
)->middleware('showRequest');
```

## Controllers

Controllers should be put inside of the `Controllers` directory. Here is an example of a controller:

```
namespace Controllers;
use Core\{View, Request};

class Home {
	public function index(Request $request){
		$title = 'Hello World!'
		View::open('index.php')->load(['title' => $title]);
	}
}
```

the `$request` argument is a class, it contains:

- parameters - extracted dynamic parameters
- path - the path without the base directory (if one was defined)
- method - the HTTP method
- data - query variables
- misc - initially `NULL`, it can contain additional information that you want to pass further on. It is more useful in middlewares rather than in controllers.
- `set_path(string $value)` - changes the path parameter, returns a URL with the new path.
- `update_data(string $key, string $value)` - changes value of a query string variable, returns the current URL with a new query string attached.

If you want to pass variables to a view. You can do this by passing an associative array - `['variable_name' => $value]` - to the `load` method as shown in the code snippet above.

## Views

Views should be put inside of the `Views` directory. To initialize the file you have to call `View::open('filename')`, the method returns a class with the following methods:

- `load(array $variables)` - renders the file
- `renderString(array $variables)` - returns the rendered HTML as a string

An example file could look like this:

```
<?php
	use Core\{Partial, View};
	$head->css('home.css', true)->title($title);
?>
<?php Partial::open('header.php')->load(); ?>
<main>
	<?php
		View::open('home/gallery.php')->load();
		View::open('home/featured.php')->load();
	?>
</main>
<?php
	View::open('home/aboutus.php')->load();
	Partial::open('footer.php')->load();
?>
```

As you can see we can add properties to the head using chainable methods. The `$head` variable is accessible in every subview/partial view and its descendants.

### Partials

Partials work exactly the same way as views do, the only difference is that they should be stored inside of the `Views/Partials` directory and should be created by the `Partial` class as shown in the above code snippet.

## Models

Are stored in the `Models` directory. They are simply classes belonging to the `Models` namespace. They can be imported to the controllers like so `use Models\Test as TestModel`.

## ORM

### SELECT

Example usage:

```
use Core\QueryBuilder;

function getProductImage($id){
	$result = QueryBuilder::select(['image_name'])
		->from('product_image')
		->where('product_ID=?', [$id])
		->orderBy('main', 'desc')
		->execute();
	return $result;
}
```

`QueryBuilder::select(columns, variables*)` - if you want to use aliases for columns, pass an associative array as the `columns` parameter like so: `['alias' => 'column']`.

- `where(condition, variables*)` - only one per query (otherwise the query will get jumbled), if you want add other conditions use the following methods:
  - `orWhere(condition, variables*)`
  - `andWhere(condition, variables*)`
- `optionalWhere(condition, variables*)` - it only applies the clause if the amount of variables given corresponds to the amount of parameters (`?`) in the "condition". Same case as with the `where` method. If you have already used `where`, don't call this method. Otherwise you will get a faulty query as a result. You should use the following methods instead:
  - `orOptionalWhere(condition, variables*)`
  - `andOptionalWhere(condition, variables*)`
- `join(table, alias, condition)` - you can not only join a table but also a [subquery](#subqueries)
  - `leftJoin(table, alias, condition)`
  - `rightJoin(table, alias, condition)`
- `groupBy(...columns)` - you can group by as many columns as you want, simply pass them as arguments one after another. The order of grouping will be the same as the order of passed arguments.
- `orderBy(column, order*)` - sorts records in ascending order by default, if you want to sort in reverse, pass 'DESC' as the second argument.
- `limit(from, to*)` - limit from, to. `to` is optional.
- `having(condition, variables*)` - works exactly like the `where` method but for `HAVING` clause
  - `orHaving(condition, variables*)`
  - `andHaving(condition, variables*)`
- `optionalHaving(condition, variables*)`
  - `orOptionalHaving(condition, variables*)`
  - `andOptionalHaving(condition, variables*)`

If you want to execute the query right away, invoke the `execute` method at the end. If you want to get the query as a string, use `getQuery`.

`variables` are values bound to parameters from query or parts of query - `?`. They are optional, hence the asterisk.

### Subqueries

You can pass a subquery to `QueryBuilder::select`, `SelectBuilder->from`, or any `join` method. Here is an example with `QueryBuilder::select`:

```
QueryBuilder::select([
	p.ID,
	'image_name' => QueryBuilder::select(['p_i.image_name'])
		->from('product_image', 'p_i')
		->where('p_i.product_ID = p.ID')
		->orderBy('p_i.main', 'DESC')
		->limit(1)
])->from('product', 'p')->execute();
```

Notice that there is no `execute` or `getQuery` at the end of a sub-query.

### EXISTS operation

You can call the `QueryBuilder::exists(table, alias)` method to return a `SELECT` statement with an `EXISTS` operator. The `table` parameter can be either a name of a table or a subquery. The method is build upon `QueryBuilder::select`, so it has access to all of its methods.

### COUNT operation

You can call the `QueryBuilder::count(table)` method to return a `SELECT` statement with a `COUNT` function. The `table` parameter can be either a name of a table or a subquery. The method is build upon `QueryBuilder::select`, so it has access to all of its methods.

### WITH recursive clause

```
$category_id = 7;
QueryBuilder::with('cte')
	->following(
		QueryBuilder::select(['id', 'name', 'parent'])->from('cte')
	)
	->recursive(
		QueryBuilder::select(['id', 'name', 'parent'])->from('category')->where('id=?', [$category_id]),
		QueryBuilder::select(['c.id', 'c.name', 'c.parent'])->from('category', 'c')->join('cte', '', 'c.id = cte.parent')
	)
	->getQuery();

# Would return:

# WITH recursive cte AS
# (
#	SELECT id,name,parent FROM category WHERE id=7
#	UNION ALL SELECT c.id,c.name,c.parent FROM category as c
#	INNER JOIN cte on c.parent = cte.id
# )
# SELECT GROUP_CONCAT(id) as array FROM cte
```

Just like with `SELECT` operation you can either execute the query immediately with `execute` or use `getQuery`.

To create a nonrecursive `WITH` clause, you can call `nonrecursive(...$anchors)`.

## Additional helper functions

These functions are available everywhere. You can extend helper files and add your own functions.

- `get_static($path)` - returns a path to a file from the static folder
- `local_href($url)` - adds `BASE_DIR` in front of the relative URL.
- `local_href_with_query_string($url)` - as the name implies.
- `redirect($url, $statusCode = 303)` - as the name implies.

### Debugging tools

You can use `dwd(mixed ...$data)` or `dd(mixed ...$data)` functions to dump info for debugging purposes. In the case of `dwd` it will be dumped at the end of the page.
