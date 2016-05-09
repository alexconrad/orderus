<?php

class Turn
{
	/**
	 * @var integer
	 */
	protected $nAttackingMemberIndex;

	/**
	 * @var integer
	 */
	protected $nDefendingMemberIndex;

	/**
	 * @var Member[]
	 */
	protected $aMembers;

	public function __construct($sAttackingMemberIndex, $aMembers)
	{
		$this->nAttackingMemberIndex = $sAttackingMemberIndex;
		$this->aMembers = $aMembers;
		$this->nDefendingMemberIndex = $this->aMembers[$sAttackingMemberIndex]->chooseOpponent($aMembers);

		return $this;
	}
	
	public function startTurn()
	{
		$this->aMembers[$this->nAttackingMemberIndex]->resetModifier();
		$this->aMembers[$this->nDefendingMemberIndex]->resetModifier();
	}

	public function attack() {
		
		$oMemberAtacker = $this->aMembers[$this->nAttackingMemberIndex];
		$oMemberDefender = $this->aMembers[$this->nDefendingMemberIndex];

		BattleLog::addTurnText($oMemberAtacker->getMemberName()." is attacking ".$oMemberDefender->getMemberName());
		
		$oMemberAtacker->attack($oMemberDefender, $this->aMembers);

	}
	
	public function endTurn()
	{
		//$this->aMembers[$this->nAttackingMemberIndex]->processModifier();
		$this->aMembers[$this->nDefendingMemberIndex]->processModifier();
	}

	public function getDefenderMemberIndex()
	{
		return $this->nDefendingMemberIndex;
	}


}
