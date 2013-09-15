<?php
namespace Mivir\Pupil\Entities;

interface LogicalOpEntityInterface
extends EntityInterface
{
	public function getOp();

	public function setToAnd();
	public function setToOr();
	public function setToNot();
}