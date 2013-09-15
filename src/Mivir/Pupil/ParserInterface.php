<?php
namespace Mivir\Pupil;

interface ParserInterface
{
	public function __construct(
		TokensInterface $tokens,
		EntityFactoryInterface $entityFactory
	);

	public function parse($tokenized);
}