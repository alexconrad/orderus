<?php
namespace Skills;

use Skills;

abstract class Base
{
	protected $chance;
	
	public function __construct() {
		return $this;
	}

	public function setChance($chance) {
		$this->chance = $chance;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function hasTrigger() {
		$rand = rand(1,100);
		return ($rand <= $this->chance);
	}

	public function getChance()
	{
		return $this->chance;
	}

	abstract public function execute(\Member $oMemberOwner, \Member $oMemberOpponent, $aMembers, $ownerIncomingDamage, $ownerOutgoingDamage);




}
