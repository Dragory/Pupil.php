<?php
namespace Mivir\Pupil\Entities;

interface TernaryEntityInterface
extends EntityInterface
{
	public function setConditions($conditions);
	public function getConditions();

	public function setThen($ifThen);
	public function getThen();
	
	public function setElse($ifElse);
	public function getElse();
}