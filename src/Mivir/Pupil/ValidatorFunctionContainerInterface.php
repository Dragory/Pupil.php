<?php
namespace Mivir\Pupil;

interface ValidatorFunctionContainerInterface
{
	public function addValidatorFunction($name, $callable);
	public function getValidatorFunction($name);
}