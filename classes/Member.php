<?php

class Member
{
	protected $sName;

	protected $sTeam;


	/** @var  \Stats\Base[] */
	protected $aStats = array();

	/** @var  \Skills\Base[] */
	protected $aSkills = array();
	
	/** @var  Modifier The things to change to the member after Turn has ended.. */
	protected $oModifer;
	
	
	public function __construct($sName, $sTeam)
	{
		$this->sName = preg_replace('/[^a-zA-Z0-9]/', '', $sName);
		$this->sTeam = preg_replace('/[^a-zA-Z0-9]/', '', $sTeam);

		return $this;
	}

	public function addStat(\Stats\Base $oStat)
	{
		$this->aStats[get_class($oStat)] = $oStat;
		return $this;
	}

	public function addSkill(\Skills\Base $oSkill)
	{
		$this->aSkills[get_class($oSkill)] = $oSkill;
		return $this;
	}


	public function getStat($sStatName)
	{
		return $this->aStats[$sStatName];
	}
	
	public function decreaseStat($sStatName, $nBy)
	{
		$this->aStats[$sStatName]->decreaseAmount($nBy);
	}

	public function getSkill($sSkillName)
	{
		return $this->aSkills[$sSkillName];
	}

	public function getStats()
	{
		return $this->aStats;
	}

	public function getSkills()
	{
		return $this->aSkills;
	}

	public function getMemberName()
	{
		return $this->sName;
	}

	public function setTeam($sTeam)
	{
		$this->sTeam = $sTeam;
	}

	public function getTeam()
	{
		return $this->sTeam;
	}

	/**
	 * @param Member[] $aMembers
	 * @return mixed
	 */
	public function chooseOpponent($aMembers)
	{
		$aPool = [];
		foreach ($aMembers as $nMemberIndex => $oMember) {
			if (($this->getMemberName() != $oMember->getMemberName()) && ($oMember->isAlive()) && ($oMember->getTeam() != $this->getTeam())) {
				$aPool[] = $nMemberIndex;
			}
		}

		shuffle($aPool);
		return array_pop($aPool);
		
	}

    /**
     *
     * @param Member $oMemberDefender
     * @param $aAllMembers
     * @param bool $bAttackerUsesSkills
     * @param bool $bDefenderUsesSkills
     * @param bool $bDefenderCanGetLucky
     */
	public function attack(\Member $oMemberDefender, $aAllMembers, $bAttackerUsesSkills = TRUE, $bDefenderUsesSkills = TRUE, $bDefenderCanGetLucky = TRUE)
	{

		$aAttackerExecutesSkills = [];
		if ($bAttackerUsesSkills) {
			foreach ($this->aSkills as $sAttackerSkillClass => $oAttackerSkill) {
				if (($oAttackerSkill instanceof \Skills\AttackSkill) && $oAttackerSkill->hasTrigger()) {
					$aAttackerExecutesSkills[] = $sAttackerSkillClass;
				}
			}
		}

		$aDefenderExecutesSkills = [];
		if ($bDefenderUsesSkills) {
			$aDefenderSkills = $oMemberDefender->getSkills();
			foreach ($aDefenderSkills as $sDefenderSkillClass => $oDefenderSkill) {
				if (($oDefenderSkill instanceof \Skills\DefenceSkill) && $oDefenderSkill->hasTrigger()) {
					$aDefenderExecutesSkills[] = $sDefenderSkillClass;
				}
			}
		}
		

		$nDamage = $this->getStat(Stats\Strength::class)->getAmount() - $oMemberDefender->getStat(Stats\Defence::class)->getAmount();
        //negative damage can be prevented here

		if ($bDefenderCanGetLucky && $oMemberDefender->isLucky()) {
			BattleLog::addTurnText($oMemberDefender->getMemberName()." is lucky !");
			//echo $oMemberDefender->getMemberName()." is lucky !<br>";
			$nDamage = 0;
		}
		$oMemberDefender->getModifier()->addDamage($nDamage);

		foreach ($aAttackerExecutesSkills as $sAttackerSkillClass) {
            /** @see \Skills\Attack\RapidStrike::execute()*/
			$this->aSkills[$sAttackerSkillClass]->execute($this, $oMemberDefender, $aAllMembers, 0, $nDamage);
		}

		foreach ($aDefenderExecutesSkills as $sDefenderSkillClass) {
            /** @see \Skills\Defence\MagicShield::execute()*/
			$oMemberDefender->getSkill($sDefenderSkillClass)->execute($oMemberDefender, $this, $aAllMembers, $nDamage, 0);
		}

	}

	public function isLucky()
	{
		if (rand(1,100) <= $this->getStat(Stats\Luck::class)->getAmount()) {
			return TRUE;
		}
		return FALSE;
	}

	public function getModifier() {
		return $this->oModifer;
	}

	public function resetModifier() {
		$this->oModifer = new Modifier();
	}

	public function processModifier()
	{
		//echo 'Processing modifiers. Damage applied to '.$this->getMemberName().': '.$this->oModifer->getDamage()."<br>";
		$this->aStats[\Stats\Health::class]->decreaseAmount($this->oModifer->getDamage());
		BattleLog::addTurnText('Processing modifiers. Damage applied to '.$this->getMemberName().': '.$this->oModifer->getDamage());
	}

	public function isAlive()
	{
		return ($this->aStats[\Stats\Health::class]->getAmount() > 0);
	}




	

}
