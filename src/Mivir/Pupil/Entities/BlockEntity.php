<?php
namespace Mivir\Pupil\Entities;

class BlockEntity
implements BlockEntityInterface
{
	protected $sub = array();

	public function setSub($sub)
	{
		$this->sub = $sub;
	}

	public function getSub()
	{
		return $this->sub;
	}

	public function addSub(EntityInterface $entity)
	{
		$this->sub[] = $entity;
	}

	public function resetSub()
	{
		$this->sub = array();
	}
}