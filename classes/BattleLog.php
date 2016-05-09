<?php

class BattleLog
{
	private static $instance;

	private static $aLog;

	/** @var MockOriginalMember[] */
	private static $oOriginalMembers;
	
	/** @var MockRound[] */
	private static $aRounds;

	private static $aLeftStanding;

	protected function __construct()
	{
	}

	private function __clone()
	{
	}

	public static function getInstance()
	{
		if (null === static::$instance) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * @param Member[] $aMembers
	 */
	public static function setOriginalMembers($aMembers)
	{
		foreach ($aMembers as $oMember) {
			self::$oOriginalMembers[] = self::memberLog($oMember);
		}
	}

	/**
	 * @param Member $oMember
	 * @return MockOriginalMember
	 */
	public static function memberLog($oMember)
	{
		$o        = new stdClass();
		$o->name  = $oMember->getMemberName();
		$o->team  = $oMember->getTeam();
		$o->stats = new stdClass();

		$aStats = $oMember->getStats();
		foreach ($aStats as $sKey => $oStat) {
			$aParts = explode('\\', $sKey);
			$sKey            = array_pop($aParts);
			$o->stats->$sKey = $oStat->getAmount();
		}

		$o->skills = new stdClass();
		$aSkills   = $oMember->getSkills();

		foreach ($aSkills as $sKey => $oSkill) {
			$aParts = explode('\\', $sKey);
			$sKey             = array_pop($aParts);
			$o->skills->$sKey = $oSkill->getChance();
		}
		return $o;
	}

	public static function addText($text) {
		self::$aLog[] = $text;
	}

	public static function getText() {
		return self::$aLog;
	}

	/**
	 * @return MockOriginalMember[]
	 */
	public static function getOriginalMembers()
	{
		return self::$oOriginalMembers;
	}
	
	public static function newRound($aOrder)
	{
		$o = new stdClass();
		/** @var  MockRound */
		$o->order = $aOrder;
		self::$aRounds[] = $o;
	}

	/**
	 * @param Member $oMemberAttacker
	 * @param Member[] $aMembers
	 */
	public static function newTurn($oMemberAttacker, $aMembers) {

		$nRoundIdx = count(self::$aRounds) - 1;

		$o = new stdClass();
		/** @var  MockTurn  $o */
		$o->attacker = $oMemberAttacker->getMemberName();
		$o->attackerStart = self::memberLog($oMemberAttacker);

		foreach ($aMembers as $oMember) {
			$o->membersStart[$oMember->getMemberName()] = self::memberLog($oMember);
		}
		self::$aRounds[$nRoundIdx]->turns[] = $o;
	}

	/**
	 * @param Member $oMemberDefender
	 */
	public static function turnDefender($oMemberDefender)
	{
		$nRoundIdx = count(self::$aRounds) - 1;
		$nTurnIdx = count(self::$aRounds[$nRoundIdx]->turns) - 1;
		self::$aRounds[$nRoundIdx]->turns[$nTurnIdx]->defender = $oMemberDefender->getMemberName();
		self::$aRounds[$nRoundIdx]->turns[$nTurnIdx]->defenderStart = self::memberLog($oMemberDefender);
	}

	/**
	 * @param Member $oMemberAttacker
	 * @param Member $oMemberDefender
	 */
	public static function turnEnd($oMemberAttacker, $oMemberDefender)
	{
		$nRoundIdx = count(self::$aRounds) - 1;
		$nTurnIdx = count(self::$aRounds[$nRoundIdx]->turns) - 1;
		self::$aRounds[$nRoundIdx]->turns[$nTurnIdx]->attackerEnd = self::memberLog($oMemberAttacker);
		self::$aRounds[$nRoundIdx]->turns[$nTurnIdx]->defenderEnd = self::memberLog($oMemberDefender);
	}

	public static function addTurnText($sText)
	{
		$nRoundIdx = count(self::$aRounds) - 1;
		$nTurnIdx = count(self::$aRounds[$nRoundIdx]->turns) - 1;
		self::$aRounds[$nRoundIdx]->turns[$nTurnIdx]->aLog[] = $sText;
	}

	public static function setEnd($sEndBattleText)
	{
		self::$aLeftStanding = $sEndBattleText;
	}

	public static function getEnd()
	{
		return self::$aLeftStanding;
	}

	public static function getRounds()
	{
		return self::$aRounds;
	}


}
