<?php
namespace Mivir\Pupil;

class EntityFactory
implements EntityFactoryInterface
{
	public function getBlockEntity()
	{
		return new Entities\BlockEntity();
	}

	public function getFuncEntity()
	{
		return new Entities\FuncEntity();
	}

	public function getTernaryEntity()
	{
		return new Entities\TernaryEntity();
	}

	public function getLogicalOpEntity()
	{
		return new Entities\LogicalOpEntity();
	}
}