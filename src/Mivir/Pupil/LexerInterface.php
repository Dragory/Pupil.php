<?php
namespace Mivir\Pupil;

interface LexerInterface
{
	public function tokenize($string);
}