<?php
namespace Mivir\Pupil;

class ArrayCacher
implements CacherInterface
{
	protected $cache = array();

	public function set($name, $value)
	{
		$this->cache[$name] = $value;
	}

	public function get($name)
	{
		if (array_key_exists($name, $this->cache)) {
			return $this->cache[$name];
		} else {
			return null;
		}
	}
}