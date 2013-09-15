<?php
namespace Mivir\Pupil;

class Tokens
implements TokensInterface
{
	protected $tokens = array(
		'IDENTIFIER' => 1,
		'COMMA' => 2,
		'COLON' => 3,
		'QUESTION_MARK' => 4,
		'STRING' => 5,
		'NUMBER' => 6,
		'LOGICAL_AND' => 7,
		'LOGICAL_OR' => 8,
		'LOGICAL_NOT' => 9,
		'BRACKET_OPEN' => 10,
		'BRACKET_CLOSE' => 11
	);

	protected $reverseTokens = array();

	public function __construct()
	{
		foreach ($this->tokens as $token => $value) {
			$this->reverseTokens[$value] = $token;
		}
	}

	public function getTokenName($value)
	{
		return $this->reverseTokens[$value];
	}

	public function __get($propertyName)
	{
		return $this->tokens[$propertyName];
	}
}