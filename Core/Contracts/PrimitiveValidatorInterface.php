<?php

namespace Core\Contracts;

interface PrimitiveValidatorInterface extends ValidatorInterface
{
  public function required(): PrimitiveValidatorInterface;
  public function checkType(): PrimitiveValidatorInterface;
  public function oneOf(array $matches): PrimitiveValidatorInterface;
  public function notOneOf(array $matches): PrimitiveValidatorInterface;
  public function regex(string $pattern): PrimitiveValidatorInterface;
}