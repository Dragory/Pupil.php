<?php
namespace Mivir\Pupil\Entities;

class LogicalOpEntity
implements LogicalOpEntityInterface
{
	protected $OP_AND = 1;
	protected $OP_OR  = 2;
	protected $OP_NOT = 3;

	protected $logicalOp = 0;

	public function getOp()
	{
		return $this->logicalOp;
	}

	public function setToAnd()
	{
		$this->logicalOp = $this->OP_AND;
	}

	public function setToOr()
	{
		$this->logicalOp = $this->OP_OR;
	}

	public function setToNot()
	{
		$this->logicalOp = $this->OP_NOT;
	}
}