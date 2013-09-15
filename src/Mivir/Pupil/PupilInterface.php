<?php
namespace Mivir\Pupil;

interface PupilInterface
{
	public function addFunction($name, $callable);

	public function validate($rules, $values);
}