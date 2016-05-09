<?php

class Modifier
{
	protected $nDamage = 0;

	public function __construct()
	{

	}

	public function processModifiers(\Member $oMember)
	{
		$oMember->getStat(\Stats\Health::class)->decreaseAmount($this->nDamage);
	}

	public function addDamage($nDamage)
	{

		//echo "Adding ".$nDamage." .. ";
		$this->nDamage = $this->nDamage + $nDamage;
		BattleLog::addTurnText("Adding ".$nDamage." damage. (Total is now: ".$this->nDamage.")");
		//echo "Total Damage:".$this->nDamage."<br>";
	}

	public function removeDamage($nDamage)
	{
		//echo "Removing ".$nDamage." .. ";
		$this->nDamage = $this->nDamage - $nDamage;
		BattleLog::addTurnText("Removing ".$nDamage." damage. (Total is now: ".$this->nDamage.")");
		//echo "Total Damage:".$this->nDamage."<br>";
	}

	public function getDamage()
	{
		return $this->nDamage;
	}




}
