<?php
namespace Mivir\Pupil;

class ValidatorFunctionContainer
{
	protected $validatorFunctions = array();

	public function addValidatorFunction($name, $callable)
	{
		$name = mb_strtolower($name);
		$this->validatorFunctions[$name] = $callable;
	}

	public function getValidatorFunction($name)
	{
		$name = mb_strtolower($name);
		return $this->validatorFunctions[$name];
	}
}