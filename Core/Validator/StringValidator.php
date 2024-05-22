<?php

namespace Core\Validator;

use Core\Abstracts\PrimitiveValidatorAbstract;

class StringValidator extends PrimitiveValidatorAbstract
{
  public function validate($data, $name = 'This value'): mixed
  {
    $this->data = $data;
    $this->name = $name;
    if (!$this->doesExist() && !$this->is_required) {
      return null;
    }
    $this->defaultValidation();
    return $this->data;
  }
  protected function isOfType(): ?\Exception
  {
    if (!is_string($this->data)) {
      throw new \Exception("{$this->name} isn't an integer: {$this->data}");
    }
    return null;
  }
  public function castDataToType(): void
  {
    $this->data = (string) $this->data;
  }
}