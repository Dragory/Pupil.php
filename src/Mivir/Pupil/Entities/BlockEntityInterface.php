<?php
namespace Mivir\Pupil\Entities;

interface BlockEntityInterface
extends EntityInterface
{
	public function setSub($sub);
	public function getSub();
	public function addSub(EntityInterface $entity);
	public function resetSub();
}