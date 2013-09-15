<?php
namespace Mivir\Pupil;

interface ValidationResultInterface
{
	public function __construct($results);

	public function isValid();
	public function hasErrors();

	public function errors();

	public function getFields();
}