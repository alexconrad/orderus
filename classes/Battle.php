<?php 

class Battle
{
	/** @var  Member[] */
	protected $aMembers;

	/** @var  Round */
	protected $oRound;

	/** @var  integer */
	protected $nNumberOfTurnsCompleted = 0;

	protected $nMaxRouns = 20;

	
	public function __construct() {
		return $this;	
	}
	
	public function attachMember(Member $oMember) 
	{
		$this->aMembers[$oMember->getMemberName()] = $oMember;
	}

	/**
	 * Checks if the battle has ended.
	 * @return bool|Member[]
	 * @throws Exception
	 */
	public function ended()
	{

		$aAliveTeams = [];
		$nAliveTeamsCount = 0;

		foreach ($this->aMembers as $sMemberIndex => $oMember) {
			if ($oMember->isAlive()) {
				if (!isset($aAliveTeams[$oMember->getTeam()])) {
					$aAliveTeams[$oMember->getTeam()] = 0;
					$nAliveTeamsCount++;
				}
				$aAliveTeams[$oMember->getTeam()]++;
			}
		}

		if ($nAliveTeamsCount > 1) {
			if ($this->nNumberOfTurnsCompleted >= $this->nMaxRouns) {
				return true;
			}
			return false;
		}

		return true;

	}

	/**
	 * @return Round
	 */
	public function increaseNumberOfTurnsCompleted()
	{
		$this->nNumberOfTurnsCompleted++;
	}

	/**
	 * @return Round
	 */
	public function getRound()
	{
		return $this->oRound;
	}
	

	public function getMembers()
	{
		return $this->aMembers;

	}

	public function printme()
	{
		echo "<table border='1' cellpadding='10'><tr>";
		foreach ($this->aMembers as $name=>$data) {
			echo '<td><b>'.$name.'</b><br>'.$data->getTeam().'<br>';
			foreach ($data->getStats() as $key=>$value) {
				echo $key."=".$value->getAmount().'<br>';
			}
			echo '</td>';
		}
		echo "</tr></table>";

	}

	public function leftAlive()
	{
		$aAlive = [];
		foreach ($this->aMembers as $sMemberIndex => $oMember) {
			if ($oMember->isAlive()) {
				$aAlive[] = $oMember->getMemberName();
			}
		}

		return $aAlive;

	}

	public function setMaxTurns($nMaxTurns)
	{
		if ($nMaxTurns <= 0) {
			$nMaxTurns = 1;
		}
		$this->nMaxRouns = $nMaxTurns;
	}


}
