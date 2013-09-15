<?php
namespace Mivir\Pupil;

class ValidationResultFactory
implements ValidationResultFactoryInterface
{
	public function getNewInstance($results)
	{
		return new ValidationResult($results);
	}
}