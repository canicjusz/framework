<?php

namespace Controllers;

use Core\{Request, View};

class Post
{
  public function index(Request $request)
  {
    $post_id = $request->parameters['id'];
    View::open('post.php')->load(['post_id' => $post_id]);
  }
}
