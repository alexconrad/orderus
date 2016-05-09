<pre>
<?php
try {

	require '../classes/Autoloader.php';
	$autoloaderPSR = new \Autoloader('', '../classes');
	$autoloaderPSR->register();

	$oOrderUs = (new Member('OrderUs', 'Red'))
		->addStat((new \Stats\Health())->randomize(70, 100))
		->addStat((new \Stats\Strength())->randomize(70, 80))
		->addStat((new \Stats\Defence())->randomize(45, 55))
		->addStat((new \Stats\Luck())->randomize(10, 30))
		->addStat((new \Stats\Speed())->randomize(40, 50))
		->addSkill((new \Skills\Defence\MagicShield())->setChance(50))
		->addSkill((new \Skills\Attack\RapidStrike())->setChance(99))
		->addStat((new \Stats\Speed())->randomize(50, 100));

	$creep1 = (new Member('Creep1', 'Blue'))
		->addStat((new \Stats\Health())->randomize(60, 90))
		->addStat((new \Stats\Strength())->randomize(60, 90))
		->addStat((new \Stats\Defence())->randomize(40, 60))
		->addStat((new \Stats\Speed())->randomize(40, 60))
		->addStat((new \Stats\Luck())->randomize(25, 40));

	$oBattle = new Battle();
	$oBattle->attachMember($oOrderUs);
	$oBattle->attachMember($creep1);

	BattleLog::setOriginalMembers($oBattle->getMembers());

	$nCheck1InfiniteLoop = 0;

	while ($oBattle->ended() === FALSE) {

		$nCheck1InfiniteLoop++;
		if ($nCheck1InfiniteLoop > 5000) {
			throw new \Exception("Likely infinite loop: ".__FILE__.":".__LINE__);
		}

		$oRound = new Round($oBattle->getMembers());
		$oRound->roundOrder();
		BattleLog::newRound($oRound->getOrder());

		$sAttackingMemberIndex = $oRound->getNextAttackingMember();
		$nCheck2InfiniteLoop = 0;

		do {

			$nCheck2InfiniteLoop++;
			if ($nCheck2InfiniteLoop > 5000) {
				throw new \Exception("Likely infinite loop: ".__FILE__.":".__LINE__);
			}

			BattleLog::newTurn($oBattle->getMembers()[$sAttackingMemberIndex], $oBattle->getMembers());
			$oTurn = new Turn($sAttackingMemberIndex, $oBattle->getMembers());
			BattleLog::turnDefender($oBattle->getMembers()[$oTurn->getDefenderMemberIndex()]);

			$oTurn->startTurn();
			$oTurn->attack();
			$oTurn->endTurn();
			BattleLog::turnEnd($oBattle->getMembers()[$sAttackingMemberIndex], $oBattle->getMembers()[$oTurn->getDefenderMemberIndex()]);

			$oBattle->increaseNumberOfTurnsCompleted();

		} while (($sAttackingMemberIndex = $oRound->getNextAttackingMember()) && ($oBattle->ended() === FALSE));

	}

	BattleLog::setEnd($oBattle->leftAlive());

	$aInitialMembers = BattleLog::getOriginalMembers();
	echo '<table cellpadding="10"><tr>'."\n";
	foreach ($aInitialMembers as $originalMember) {
		echo '<td valign="top">';
		echo "Name:<b>".$originalMember->name.'</b>'."<br>\n";
		echo "Team:".$originalMember->team."<br>\n";
		foreach ($originalMember->stats as $nName=>$nValue) {
			echo $nName.':'.$nValue."<br>\n";
		}
		foreach ($originalMember->skills as $nName=>$nValue) {
			echo $nName.':'.$nValue."<br>\n";
		}
		echo '</td>'."\n";
	}
	echo '</tr></table>';

	$aRounds = BattleLog::getRounds();

	foreach ($aRounds as $nNrRound=> $oRound) {
		echo "\n\nRound : ".($nNrRound+1)."\n";
		echo "Order : ".implode(",",$oRound->order)."\n";
		foreach ($oRound->turns as $nNrTurn=>$oTurn) {
			echo "\n\nTurn:".($nNrTurn+1)."\n";
			echo 'Defender '.$oTurn->defenderStart->name.' starts with '.$oTurn->defenderStart->stats->Health.' Health'."\n";
			echo implode("\n", $oTurn->aLog);
			echo "\n".$oTurn->defenderEnd->name.' left with '.$oTurn->defenderEnd->stats->Health.' Health'."\n";
		}
	}

	$aEnd = BattleLog::getEnd();
	echo "\n\nStill standing : ".implode(",", $aEnd);


}catch (\Exception $e) {
	echo $e->getMessage();
}
