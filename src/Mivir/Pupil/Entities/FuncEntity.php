<?php
namespace Mivir\Pupil\Entities;

class FuncEntity
implements FuncEntityInterface
{
	protected $name = array();
	protected $args = array();

	public function setName($name)
	{
		$this->name = $name;
	}

	public function setArgs($args)
	{
		$this->args = $args;
	}

	public function addArg($arg)
	{
		$this->args[] = $arg;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getArgs()
	{
		return $this->args;
	}
}