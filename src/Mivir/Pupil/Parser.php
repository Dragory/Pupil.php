<?php
namespace Mivir\Pupil;

class Parser
implements ParserInterface
{
	protected $tokens = null;

	protected $entityFactory = null;

	public function __construct(
		TokensInterface $tokens,
		EntityFactoryInterface $entityFactory
	)
	{
		$this->tokens = $tokens;
		$this->entityFactory = $entityFactory;
	}

	public function parse($tokenized)
	{
		// A shorthand
		$tokens = $this->tokens;

		$rootBlock = $this->entityFactory->getBlockEntity();
		$blockStack = array($rootBlock);

		$currentBlock = end($blockStack);

		$currentFunction = null;
		$flushFunction   = true;

		$accept = [
			'identifier' => 0,
			'string' => 0,
			'number' => 0,
			'logicalOp' => 0,
			'negator' => 0,
			'funcArgsStart' => 0, // Starts a function's arguments after its name ('(')
			'funcArgsEnd' => 0,   // Ends a function's arguments (')')
			'argSeparator' => 0,  // Separates arguments from each other (',')
			'blockStart' => 0,
			'blockEnd' => 0,
			'ternaryThen' => 0,
			'ternaryElse' => 0
		];

		$expectOneOf = function($oneOf) use (&$accept) {
			// O(n) to be able to O(1) the "one of" values later on
			$oneOfKeys = [];
			foreach ($oneOf as $key => $value) {
				$oneOfKeys[$value] = true;
			}

			array_walk($accept, function(&$value, $key) use ($oneOfKeys) {
				if (isset($oneOfKeys[$key])) {
					$value = 1;
				} else {
					$value = 0;
				}
			});
		};

		$expectOneOf(['identifier', 'negator', 'blockStart']);

		// Loop through the tokens in the tokenized string
		for ($i = 0; $i < count($tokenized); $i++) {
			$thisToken = $tokenized[$i];
			$entitiesToPush = [];

			$openBlock  = false;
			$closeBlock = false;

			$closeTernary = false;

			// Arguments for an already created function
			if ($thisToken->type === $tokens->STRING || $thisToken->type === $tokens->NUMBER) {
				if ( ! $accept['string'] || ! $accept['number']) {
					throw new ParserException("Unexpected string or number.", $i);
				}

				$flushFunction = false;

				// String arguments
				if ($thisToken->type === $tokens->STRING) {
					$currentFunction->addArg((string)$thisToken->data);

				// Number arguments
				} else {
					$currentFunction->addArg((float)$thisToken->data);
				}

				$expectOneOf(['argSeparator', 'funcArgsEnd']);

			// A new function
			} else if ($thisToken->type === $tokens->IDENTIFIER) {
				if ( ! $accept['identifier']) {
					throw new ParserException("Unexpected identifier.", $i);
				}

				$flushFunction = false;

				$currentFunction = $this->entityFactory->getFuncEntity();
				$currentFunction->setName((string)$thisToken->data);

				$expectOneOf(['logicalOp', 'funcArgsStart', 'blockEnd', 'ternaryThen', 'ternaryElse']);

			// Ternary "then"/start
			} else if ($thisToken->type === $tokens->QUESTION_MARK) {
				if ( ! $accept['ternaryThen']) {
					throw new ParserException("Unexpected ternary 'then'", $i);
				}

				// Start a new ternary with the current block's "sub" entities
				// as the ternary's conditions.
				$newTernary = $this->entityFactory->getTernaryEntity();
				$newTernary->setConditions($currentBlock->getSub());


				// Reset the current block's sub entities so that they don't get
				// evaluated outside of the ternary and add the ternary as the block's
				// sub entity.
				$currentBlock->resetSub();
				$currentBlock->addSub($newTernary);

				// Set the ternary as the current block
				$blockStack[] = $newTernary;
				$currentBlock = end($blockStack);

				$expectOneOf(['identifier', 'blockStart', 'negator']);

			// Ternary "else"
			} else if ($thisToken->type === $tokens->COLON) {
				if ( ! $accept['ternaryElse']) {
					throw new ParserException("Unexpected ternary 'else'", $i);
				}

				$currentBlock->setThen($currentBlock->getSub());
				$currentBlock->resetSub();

				$expectOneOf(['identifier', 'blockStart', 'negator']);

			// Function argument separator
			} else if ($thisToken->type === $tokens->COMMA) {
				if ( ! $accept['argSeparator']) {
					throw new ParserException("Unexpected function argument separator.", $i);
				}

				$flushFunction = false;

				$expectOneOf(['string', 'number', 'funcArgsEnd']);

			// Logical AND
			} else if ($thisToken->type === $tokens->LOGICAL_AND) {
				if ( ! $accept['logicalOp']) {
					throw new ParserException("Unexpected logical AND", $i);
				}

				$newLogicalEntity = $this->entityFactory->getLogicalOpEntity();
				$newLogicalEntity->setToAnd();

				$entitiesToPush[] = $newLogicalEntity;
				
				$flushFunction = true;

				$expectOneOf(['identifier', 'negator', 'blockStart']);

			// Logical OR
			} else if ($thisToken->type === $tokens->LOGICAL_OR) {
				if ( ! $accept['logicalOp']) {
					throw new ParserException("Unexpected logical OR", $i);
				}

				$newLogicalEntity = $this->entityFactory->getLogicalOpEntity();
				$newLogicalEntity->setToOr();

				$entitiesToPush[] = $newLogicalEntity;

				$flushFunction = true;

				$expectOneOf(['identifier', 'blockStart', 'negator']);

			// Logical NOT (negator)
			} else if ($thisToken->type === $tokens->LOGICAL_NOT) {
				if ( ! $accept['negator']) {
					throw new ParserException("Unexpected logical NOT", $i);
				}

				$newLogicalEntity = $this->entityFactory->getLogicalOpEntity();
				$newLogicalEntity->setToNot();

				$entitiesToPush[] = $newLogicalEntity;

				$expectOneOf(['identifier', 'blockStart']);

			// Bracket open: function arguments start or a block start
			} else if ($thisToken->type === $tokens->BRACKET_OPEN) {
				if ($currentFunction && $accept['funcArgsStart']) {
					$flushFunction = false;

					$expectOneOf(['string', 'number', 'funcArgsEnd']);
				} else if ($accept['blockStart']) {
					$openBlock = true;
					$flushFunction = true;

					$expectOneOf(['identifier', 'blockStart', 'blockEnd', 'negator']);
				} else {
					var_dump($currentFunction);
					var_dump($accept);
					throw new ParserException("Unexpected opening bracket", $i);
				}

			// Bracket close: function arguments end or a block end
			} else if ($thisToken->type === $tokens->BRACKET_CLOSE) {
				if ($currentFunction && $accept['funcArgsEnd']) {
					$flushFunction = true;

					$expectOneOf(['logicalOp', 'blockEnd', 'ternaryThen', 'ternaryElse']);
				} else if ($accept['blockStart']) {
					$openBlock = true;
					$flushFunction = true;

					$expectOneOf(['identifier', 'blockStart', 'blockEnd', 'negator']);
				} else {
					throw new ParserException("Unexpected opening bracket", $i);
				}
			}

			if ($i == count($tokenized) - 1) {
				$flushFunction = true;
				$closeTernary = true;
			}

			if ($flushFunction && $currentFunction) {
				array_unshift($entitiesToPush, $currentFunction);
				$currentFunction = null;
			}

			if (count($entitiesToPush) > 0) {
				for ($a = 0; $a < count($entitiesToPush); $a++) {
					$currentBlock->addSub($entitiesToPush[$a]);
				}
			}

			if ($openBlock) {
				$newBlock = $this->entityFactory->getBlockEntity();
				$currentBlock->addSub($newBlock);

				$blockStack[] = $newBlock;
				$currentBlock = end($blockStack);
			}

			if (($closeTernary || $closeBlock) && $currentBlock instanceof Entities\TernaryEntityInterface) {
				if ($currentBlock->getThen() !== null) {
					$currentBlock->setElse($currentBlock->getSub());
				} else {
					$currentBlock->setThen($currentBlock->getSub());
				}

				$currentBlock->resetSub();

				array_pop($blockStack);
				$currentBlock = end($blockStack);
			}

			if ($closeBlock) {
				if (count($blockStack) === 1) {
					throw new ParserException("Can't close the root block.");
				}

				array_pop($blockStack);
				$currentBlock = end($blockStack);
			}
		} // End token loop

		if (count($blockStack) > 1) {
			throw new ParserException("All block weren't closed.");
		}

		return [$rootBlock];
	}
}