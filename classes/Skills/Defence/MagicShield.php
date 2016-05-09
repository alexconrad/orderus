<?php

namespace Skills\Defence;

use Skills;

class MagicShield extends Skills\DefenceSkill
{
	/**
	 * @param \Member $oMemberOwner
	 * @param \Member $oMemberOpponent
	 * @param \Member[] $aMembers
	 */
	public function execute(\Member $oMemberOwner, \Member $oMemberOpponent, $aMembers, $ownerIncomingDamage, $ownerOutgoingDamage)
	{
		\BattleLog::addTurnText($oMemberOwner->getMemberName()." executes skill ".__CLASS__);
		//echo $oMemberOwner->getMemberName()." executes skill ".__CLASS__."<br>";
		$oMemberOwner->getModifier()->removeDamage(round($ownerIncomingDamage / 2, 2));
	}

}
