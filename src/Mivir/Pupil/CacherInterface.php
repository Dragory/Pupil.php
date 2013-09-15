<?php
namespace Mivir\Pupil;

interface CacherInterface
{
	public function set($name, $value);
	public function get($name);
}