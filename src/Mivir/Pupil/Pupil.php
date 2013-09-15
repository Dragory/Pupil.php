<?php
namespace Mivir\Pupil;

class Pupil
implements PupilInterface
{
	protected $tokens = array();

	protected $validatorFunctionContainer = null;

	protected $lexer = null;

	protected $parser = null;

	protected $validator = null;

	protected $cacher = null;

	public function __construct(
		CacherInterface $cacher = null,
		LexerInterface $lexer = null,
		ParserInterface $parser = null,
		ValidatorInterface $validator = null,
		ValidatorFunctionContainerInterface $validatorFunctionContainer = null
	)
	{
		$this->tokens = new Tokens();

		if ( ! $parser) {
			$entityFactory = new EntityFactory();
			$this->parser  = new Parser($this->tokens, $entityFactory);
		} else {
			$this->parser = $parser;
		}

		$this->validatorFunctionContainer = ($validatorFunctionContainer ? $validatorFunctionContainer : new DefaultValidatorFunctionContainer());

		$this->lexer     = ($lexer     ? $lexer     : new Lexer($this->tokens));
		$this->validator = ($validator ? $validator : new Validator($this->validatorFunctionContainer));
		$this->cacher    = ($cacher    ? $cacher    : new ArrayCacher());
	}

	public function addFunction($name, $callable)
	{
		$this->validatorFunctionContainer->addValidatorFunction($name, $callable);
	}

	public function validate($rules, $values)
	{
		$results = array();

		// Default the validation results for all of the values to "passing"
		foreach ($values as $index => $value) {
			$results[$index] = true;
		}

		// And then run the rules
		foreach ($rules as $index => $ruleString) {
			if ( ! array_key_exists($index, $values)) {
				$values[$index] = '';
			}

			$value = $values[$index];
			$parsedRule = null;
			$cachedParsedRule = $this->cacher->get($ruleString);

			if ($cachedParsedRule) {
				$parsedRule = $cachedParsedRule;
			} else {
				$tokenized = $this->lexer->tokenize($ruleString);
				$parsed = $this->parser->parse($tokenized);

				$parsedRule = $parsed;
			}

			$results[$index] = $this->validator->validate($parsedRule, $values, $index);
		}

		return $results;
	}
}