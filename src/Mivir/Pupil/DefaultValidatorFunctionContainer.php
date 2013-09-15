<?php
namespace Mivir\Pupil;

class DefaultValidatorFunctionContainer
extends ValidatorFunctionContainer
implements ValidatorFunctionContainerInterface
{
	protected $regexes = array(
		'alpha' => '^[a-zA-Z]+$',
		'alphanumeric' => '^[a-zA-Z0-9]+$',
		'email' => '^.*?@.*?\..+$'
	);

	public function __construct()
	{
		$regexes = $this->regexes;

		$this->addValidatorFunction('equals', function($allValues, $value, $equalsTo) {
			return $value == $equalsTo;
		});

		$this->addValidatorFunction('iequals', function($allValues, $value, $equalsTo) {
		    return mb_strtolower($value) == mb_strtolower($equalsTo);
		});

		$this->addValidatorFunction('sequals', function($allValues, $value, $equalsTo) {
		    return $value === $equalsTo;
		});

		$this->addValidatorFunction('siequals', function($allValues, $value, $equalsTo) {
		    return mb_strtolower($value) === mb_strtolower($allValues);
		});

		$this->addValidatorFunction('lenmin', function($allValues, $value, $min) {
		    return mb_strlen($value) >= $min;
		});

		$this->addValidatorFunction('lenmax', function($allValues, $value, $max) {
		    return mb_strlen($value) <= $max;
		});

		$this->addValidatorFunction('lenequals', function($allValues, $value, $equalsTo) {
		    return mb_strlen($value) == intval($equalsTo);
		});

		$this->addValidatorFunction('min', function($allValues, $value, $min) {
		    return floatval($value) >= $min;
		});

		$this->addValidatorFunction('max', function($allValues, $value, $max) {
		    return floatval($value) <= $max;
		});

		$this->addValidatorFunction('between', function($allValues, $value, $min, $max) {
			$numVal = floatval($value);
		    return (($numVal >= $min) && ($numVal <= $max));
		});

		$this->addValidatorFunction('in', function(/*$allValues, $value, $inList...*/) {
			$args = func_get_args();

		    $allValues = array_shift($args);
		    $value = array_shift($args);

		    $inList = $args;
		    for ($i = 0; $i < count($inList); $i++) {
		        if ($inList[$i] == $value) return true;
		    }

		    return false;
		});

		$this->addValidatorFunction('required', function($allValues, $value) {
		    return !!$value;
		});

		$this->addValidatorFunction('optional', function($allValues, $value) {
		    return true;
		});

		$this->addValidatorFunction('numeric', function($allValues, $value) {
		    return is_numeric($value);
		});

		$this->addValidatorFunction('alpha', function($allValues, $value) use ($regexes) {
		    return preg_match("/" . $regexes['alpha'] . "/", $value);
		});

		$this->addValidatorFunction('alphanumeric', function($allValues, $value) use ($regexes) {
		    return preg_match("/" . $regexes['alphanumeric'] . "/", $value);
		});

		$this->addValidatorFunction('email', function($allValues, $value) use ($regexes) {
		    return preg_match("/" . $regexes['email'] . "/", $value);
		});

		$this->addValidatorFunction('regex', function($allValues, $value, $regex, $flags) {
			$flags = $flags ? $flags : "";
			$flags = str_replace('e', '', $flags); // No eval flag
		    
		    return preg_match("/{$regex}/{$flags}", $value);
		});

		$this->addValidatorFunction('integer', function($allValues, $value) {
			return intval($value) == $value;
		});

		$this->addValidatorFunction('equalsto', function($allValues, $value, $equalsToKey) {
			if (array_key_exists($equalsToKey, $allValues)) {
				return $value == $allValues[$equalsToKey];
			} else {
				return $value == null;
			}
		});
	}
}