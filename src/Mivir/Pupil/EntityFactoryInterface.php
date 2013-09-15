<?php
namespace Mivir\Pupil;

interface EntityFactoryInterface
{
	public function getBlockEntity();
	public function getFuncEntity();
	public function getTernaryEntity();
	public function getLogicalOpEntity();
}