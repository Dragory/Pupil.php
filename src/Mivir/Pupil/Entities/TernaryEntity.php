<?php
namespace Mivir\Pupil\Entities;

class TernaryEntity
// We extend the block entity so we can behave like blocks but then later on
// use our own conditions and then and else features.
extends BlockEntity
implements TernaryEntityInterface
{
	protected $conditions = array();
	protected $ifThen     = null;
	protected $ifElse     = null;

	public function setConditions($conditions)
	{
		$this->conditions = $conditions;
	}

	public function setThen($ifThen)
	{
		$this->ifThen = $ifThen;
	}

	public function setElse($ifElse)
	{
		$this->ifElse = $ifElse;
	}

	public function getConditions()
	{
		return $this->conditions;
	}

	public function getThen()
	{
		return $this->ifThen;
	}

	public function getElse()
	{
		return $this->ifElse;
	}
}