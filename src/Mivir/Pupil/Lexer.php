<?php
namespace Mivir\Pupil;

class Lexer
implements LexerInterface
{
	protected $tokens = array();

	public function __construct(TokensInterface $tokens)
	{
		$this->tokens = $tokens;
	}

	public function tokenize($string)
	{
		// A UTF-8 split to chars
		$chars = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);

		$whiteSpaceRegex = '/^\s+$/';

		// A shorthand
		$tokens = $this->tokens;

		// The tokens we're going to return
		$resultTokens = array();

		// If we're "building" an identifier, store it here until we flush it to a token.
		$tempIdentifier = "";

		// Keep building the identifier?
		$appendToTempIdentifier = false;

		// When a char is escaped, treat it as an identifier even if it would
		// otherwise be resolved to a different token
		$treatNextAsIdentifier = false;

		// The token or tokens to push at the end of the loop
		// after e.g. flushing the identifier
		$tokensToPush = array();

		// Sometimes we'll completely ignore a char, such as with escape symbols
		$ignoreThisChar = false;

		// Are we in a string?
		$inString = false;
		$stringStartChar = null;

		// Loop through the chars
		for ($i = 0; $i < count($chars); $i++) {
			$thisChar = $chars[$i];
			$nextChar = (array_key_exists($i + 1, $chars) ? $chars[$i + 1] : null);

			// If we should start or end a string at the end of this loop
			$startString = false;
			$endString = false;

			// Reset some variables for this loop
			$appendToTempIdentifier = false;
			$tokensToPush = array();
			$ignoreThisChar = false;

			// This char was escaped, count as an identifier.
			if ($treatNextAsIdentifier) {
			    $treatNextAsIdentifier = false;
			    $appendToTempIdentifier = true;
			
			// String end
			} else if ($thisChar === $stringStartChar) {
				$endString = true;

			// In a string
			} else if ($inString) {
				$appendToTempIdentifier = true;

			// String start
			} else if ($thisChar === '"' || $thisChar === "'") {
				$startString = true;

			// Escape the next char; ignore this one (because it's an escaping symbol)
			// and don't flush the identifier (as the next char will be added to it).
			} else if ($thisChar == '\\') {
			    $treatNextAsIdentifier = true;
			    $ignoreThisChar = true;
			
			// General tokens
			} else if ($thisChar == ',') {
			    $tokensToPush[] = array($tokens->COMMA);
			} else if ($thisChar == ':') {
			    $tokensToPush[] = array($tokens->COLON);
			} else if ($thisChar == '?') {
				$tokensToPush[] = array($tokens->QUESTION_MARK);
			} else if ($thisChar == '&' && $nextChar == '&') {
			    $tokensToPush[] = array($tokens->LOGICAL_AND);
			    $i++;
			} else if ($thisChar == '|' && $nextChar == '|') {
			    $tokensToPush[] = array($tokens->LOGICAL_OR);
			    $i++;
			} else if ($thisChar == '!') {
			    $tokensToPush[] = array($tokens->LOGICAL_NOT);
			} else if ($thisChar == '(') {
			    $tokensToPush[] = array($tokens->BRACKET_OPEN);
			} else if ($thisChar == ')') {
			    $tokensToPush[] = array($tokens->BRACKET_CLOSE);

			// Ignore whitespace unless we're in a string
			} else if (preg_match($whiteSpaceRegex, $thisChar)) {
				$ignoreThisChar = true;
			
			// Otherwise it's an identifier part
			} else {
				$appendToTempIdentifier = true;
			}

			// Should we build the identifier with this char?
			if ($appendToTempIdentifier) {
			    $tempIdentifier .= $thisChar;
			}

			// Make sure we flush the identifier if we still have one
			// going when the string ends.
			if ($i == count($chars) - 1) {
			    $appendToTempIdentifier = false;
			}

            // Flushing the identifier means pushing an identifier
            // token with the current "tempIdentifier" as the data
            // and then emptying the temporary identifier.
            // 
            // The identifier can be pushed as a string, a number or an identifier.
            if ( ! $appendToTempIdentifier && ! $ignoreThisChar && $tempIdentifier !== "") {
            	if ($inString) {
            		array_unshift($tokensToPush, [$tokens->STRING, $tempIdentifier]);
            	} else if (is_numeric($tempIdentifier)) {
            		array_unshift($tokensToPush, [$tokens->NUMBER, $tempIdentifier]);
            	} else {
            		array_unshift($tokensToPush, [$tokens->IDENTIFIER, $tempIdentifier]);
            	}

                $tempIdentifier = "";
            }

            if ($startString) {
            	$inString = true;
            	$stringStartChar = $thisChar;
            }

            if ($endString) {
            	$inString = false;
            	$stringStartChar = null;
            }

            // Push outstanding tokens
            if (count($tokensToPush) > 0) {
            	for ($a = 0; $a < count($tokensToPush); $a++) {
            		$newResultToken = new \stdClass();
            		$newResultToken->type = $tokensToPush[$a][0];
            		$newResultToken->data = (isset($tokensToPush[$a][1]) ? $tokensToPush[$a][1] : null);

            		$resultTokens[] = $newResultToken;
            	}
            }
		} // End the char loop

		return $resultTokens;
	}
}