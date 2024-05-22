<?php

namespace Core\Contracts;

interface ValidatorInterface
{
  public function validate($data): mixed;
}