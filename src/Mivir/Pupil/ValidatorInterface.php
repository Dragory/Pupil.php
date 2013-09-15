<?php
namespace Mivir\Pupil;

interface ValidatorInterface
{
	public function __construct(ValidatorFunctionContainerInterface $validatorFunctionContainer);

	public function validate($parsed, $values, $valueKey);
}