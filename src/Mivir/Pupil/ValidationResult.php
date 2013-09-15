<?php
namespace Mivir\Pupil;

class ValidationResult
implements ValidationResultInterface, \Iterator
{
	protected $position = 0;

	protected $results = array();

	public function __construct($results)
	{
		foreach ($results as $field => $result) {
			$this->results[] = array($field, $result);
		}
	}

	public function isValid()
	{
		foreach ($this->results as $result) {
			if ( ! $result[1]) return false;
		}

		return true;
	}

	public function hasErrors()
	{
		return ! $this->isValid();
	}

	/**
	 * Return fields with validation errors.
	 *
	 * @return  array  An array of fields with validation errors
	 */
	public function errors()
	{
		$errors = array();

		foreach ($this->results as $result) {
			if ( ! $result[1]) {
				$errors[] = $result[0];
			}
		}

		return $errors;
	}

	/**
	 * Returns the validated fields.
	 *
	 * @return  array  An array containing the validated fields
	 */
	public function getFields()
	{
		$fields = array();

		foreach ($this->results as $result) {
			$fields[$result[0]] = $result[1];
		}

		return $fields;
	}

	/**
	 * Iterator methods
	 */

	public function rewind()
	{
		$this->position = 0;
	}

	public function current()
	{
		$thisResult = $this->results[$this->position];
		return array($thisResult[0] => $thisResult[1]);
	}

	public function key()
	{
		return $this->position;
	}

	public function next()
	{
		$this->position++;
	}

	public function valid()
	{
		return isset($this->results[$this->position]);
	}
}