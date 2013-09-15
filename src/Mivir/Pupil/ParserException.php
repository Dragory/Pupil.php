<?php
namespace Mivir\Pupil;

class ParserException
extends \Exception
{
	protected $tokenNumber;

	public function __construct($message, $tokenNumber = 0)
	{
		parent::__construct($message);
		$this->tokenNumber = $tokenNumber;
	}

	public function getTokenNumber()
	{
		return $this->tokenNumber;
	}
}