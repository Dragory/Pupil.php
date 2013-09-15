<?php
namespace Mivir\Pupil\Entities;

interface FuncEntityInterface
extends EntityInterface
{
	public function setName($name);
	public function getName();

	public function setArgs($args);
	public function addArg($arg);
	public function getArgs();
}