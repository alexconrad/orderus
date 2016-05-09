<?php

namespace Skills\Attack;

use Skills;

class RapidStrike extends Skills\AttackSkill
{

	public function execute(\Member $oMemberOwner, \Member $oMemberOpponent, $aMembers, $ownerIncomingDamage, $ownerOutgoingDamage)
	{
		\BattleLog::addTurnText($oMemberOwner->getMemberName()." executes skill ".__CLASS__);

		//echo $oMemberOwner->getMemberName()." executes skill ".__CLASS__."<br>";

		$oMemberOwner->attack($oMemberOpponent, $aMembers, FALSE, TRUE, TRUE);
	}

}
