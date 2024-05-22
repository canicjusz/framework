<?php

namespace Core\Abstracts;

use Core\Contracts\PrimitiveValidatorInterface;


abstract class PrimitiveValidatorAbstract implements PrimitiveValidatorInterface
{
  protected $is_required = false;
  protected $strict_mode = false;
  protected $mandatory_matches;
  protected $disallowed_matches;
  protected $pattern;
  protected $data;
  protected $name;

  protected function defaultValidation()
  {
    if (!$this->doesExist() && $this->is_required) {
      throw new \Exception("{$this->name} is empty or set to NULL");
    }
    if (!is_scalar($this->data)) {
      throw new \Exception("{$this->name} must be either: int, float, string or bool, not " . gettype($this->data));
    }
    if (!$this->strict_mode) {
      $this->castDataToType();
    }
    $this->isOfType();
    if (isset($this->mandatory_matches)) {
      $this->isOneOfMandatoryMatches();
    }
    if (isset($this->disallowed_matches)) {
      $this->isNotOneOfDisallowedMatches();
    }
    if (isset($this->pattern)) {
      $this->doesMatchRegex();
    }
  }
  protected function doesExist()
  {
    return isset($this->data) && (!empty($this->data) || $this->data === false);
  }
  protected function isOneOfMandatoryMatches(): ?\Exception
  {
    if (!in_array($this->data, $this->mandatory_matches, !$this->strict_mode)) {
      $arr_to_str = "[" . implode(',', $this->mandatory_matches) . "]";
      throw new \Exception("{$this->name} isn't one of mandatory matches. {$this->data} doesn't exist in $arr_to_str");
    }
    return null;
  }
  protected function isNotOneOfDisallowedMatches(): ?\Exception
  {
    if (in_array($this->data, $this->disallowed_matches, !$this->strict_mode)) {
      $arr_to_str = "[" . implode(',', $this->disallowed_matches) . "]";
      throw new \Exception("{$this->name} is one of disallowed matches. {$this->data} doesn't exist in $arr_to_str");
    }
    return null;
  }
  protected function doesMatchRegex(): ?\Exception
  {
    if (!preg_match($this->pattern, $this->data)) {
      throw new \Exception("{$this->name} doesn't match the provided regex. {$this->data} doesn't correspond to the pattern: {$this->pattern}");
    }
    return null;
  }

  abstract protected function isOfType(): ?\Exception;
  abstract protected function castDataToType(): void;

  public function required(): PrimitiveValidatorInterface
  {
    $this->is_required = true;
    return $this;
  }
  public function checkType(): PrimitiveValidatorInterface
  {
    $this->strict_mode = true;
    return $this;
  }
  public function oneOf(array $matches): PrimitiveValidatorInterface
  {
    $this->mandatory_matches = $matches;
    return $this;
  }
  public function notOneOf(array $matches): PrimitiveValidatorInterface
  {
    $this->disallowed_matches = $matches;
    return $this;
  }
  public function regex(string $pattern): PrimitiveValidatorInterface
  {
    $this->pattern = $pattern;
    return $this;
  }
}