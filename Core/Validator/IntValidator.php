<?php declare(strict_types=1);

namespace Core\Validator;

use Core\Abstracts\PrimitiveValidatorAbstract;

class IntValidator extends PrimitiveValidatorAbstract
{
  public function validate($data, $name = 'This value'): mixed
  {
    $this->data = $data;
    $this->name = $name;
    if (!$this->doesExist() && !$this->is_required) {
      print_r(is_bool($data));
      return null;
    }
    $this->defaultValidation();
    return $this->data;
  }
  protected function isOfType(): ?\Exception
  {
    if (!is_int($this->data)) {
      throw new \Exception("{$this->name} isn't an integer: {$this->data}");
    }
    return null;
  }
  public function castDataToType(): void
  {
    $this->data = (int) $this->data;
  }
}