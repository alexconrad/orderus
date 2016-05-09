<?php
namespace Stats;

use Stats;

abstract class Base
{
	protected $amount;

	public function __construct()
	{
		return $this;
	}

	public function randomize($min, $max)
	{
		$this->amount = rand($min, $max);
		return $this;
	}

	public function getAmount()
	{
		return $this->amount;
	}

	public function decreaseAmount($nBy)
	{
		$this->amount = $this->amount - $nBy;
	}


}
