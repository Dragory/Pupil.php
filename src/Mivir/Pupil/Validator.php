<?php
namespace Mivir\Pupil;

class Validator
implements ValidatorInterface
{
	protected $validatorFunctionContainer = null;

	public function __construct(ValidatorFunctionContainerInterface $validatorFunctionContainer)
	{
		$this->validatorFunctionContainer = $validatorFunctionContainer;
	}

	public function validate($parsed, $values, $valueKey)
	{
		if ($parsed === null) {
			return true;
		}

		$validatorFunctions = $this->validatorFunctionContainer;

		$validationResult = true;
		$previousLogicalOperator = 1;
		$negateNext = false;

		// Loop through the parsed entities
		for ($i = 0; $i < count($parsed); $i++) {
			$thisEntity = $parsed[$i];

			$tempResult = true;
			$useTempResult = false;

			// Logical operators
			if ($thisEntity instanceof Entities\LogicalOpEntityInterface) {
				$logicalOpType = $thisEntity->getOp();

				if ($logicalOpType == $thisEntity->OP_AND) {
					$previousLogicalOperator = 1;
				} else if ($logicalOpType == $thisEntity->OP_OR) {
					$previousLogicalOperator = 2;
				} else if ($logicalOpType == $thisEntity->OP_NOT) {
					$negateNext = true;
				}

			// Functions
			} else if ($thisEntity instanceof Entities\FuncEntityInterface) {
				$funcName = mb_strtolower($thisEntity->getName());
				$funcArgs = $thisEntity->getArgs();

				if (mb_substr($funcName, 0, 5) == 'other') {
					$funcName = mb_substr($funcName, 5);

					$otherValueKey = array_shift($funcArgs);
					if (array_key_exists($otherValueKey, $values)) {
						array_unshift($funcArgs, $values[$otherValueKey]);
					} else {
						array_unshift($funcArgs, '');
					}
				} else {
					array_unshift($funcArgs, $values[$valueKey]);
				}

				array_unshift($funcArgs, $values);

				$func = $this->validatorFunctionContainer->getValidatorFunction($funcName);

				$tempResult = call_user_func_array($func, $funcArgs);
				$useTempResult = true;

			// Ternaries
			} else if ($thisEntity instanceof Entities\TernaryEntityInterface) {
				$ternaryCondition = $this->validate($thisEntity->getConditions(), $values, $valueKey);

				if ($ternaryCondition) {
					$tempResult = $this->validate($thisEntity->getThen(), $values, $valueKey);
				} else {
					$tempResult = $this->validate($thisEntity->getElse(), $values, $valueKey);
				}

				$useTempResult = true;
			// Blocks
			} else if ($thisEntity instanceof Entities\BlockEntityInterface) {
				$tempResult = $this->validate($thisEntity->getSub(), $values, $valueKey);
				$useTempResult = true;
			}

			if ($useTempResult) {
				if ($negateNext) {
					$tempResult = ! $tempResult;
					$negateNext = false;
				}

				if ($previousLogicalOperator == 1) {
					$validationResult = $validationResult && $tempResult;
				} else if ($previousLogicalOperator == 2) {
					$validationResult = $validationResult || $tempResult;
				}

				$useTempResult = false;
			}
		} // End entity loop

		return $validationResult;
	}
}