<?php



class Control
{
	/** @var  View */
	protected $oView;

	public function __construct()
	{
		$this->oView = new View(APP_PATH.'/views');
	}

	public function actionSetup()
	{
		$sDir = APP_PATH."/battlelogs/";
		$battleLogs = [];
		if (is_dir($sDir)) {
			if ($dh = opendir($sDir)) {
				while (($file = readdir($dh)) !== false) {
					if (stristr($file, '.json')) {
						$aParts = explode('.', $file);
						$battleLogs[] = array_shift($aParts);
					}
				}
				closedir($dh);
			}
		}

		$this->oView->assign('battleLogs', $battleLogs);
		$this->oView->display('setup');
	}

	public function actionReplay()
	{
		$sFname = $_GET['fname'];
		$sFname = preg_replace('/[^0-9]/', '', $sFname);

		$sFilename = APP_PATH."/battlelogs/".$sFname.".json";
		if (!file_exists($sFilename)) {
			throw new Exception("No such battlelog.", 5050);
		}

		$sJson = file_get_contents($sFilename);
		$aJson = json_decode($sJson);

		$this->oView->assign('originalMembers', $aJson->orim);
		$this->oView->assign('aRounds', $aJson->rounds);
		$this->oView->assign('stillstanding', $aJson->stillstand);

		$this->oView->display('battle');
	}

	public function actionBattle()
	{
		$post = $_POST['members'];


		$oBattle = new Battle();
		$oBattle->setMaxTurns((int)$_POST['maxRounds']);

		foreach ($post as $sMemberName => $aData) {

			$oMember = new Member($aData['name'], $aData['team']);
			
			foreach ($aData['stat'] as $sStatName => $sValue) {

				if (stristr($sValue, '..')) {
					list($min,$max) = explode('..', $sValue);
				}else {
					$min = $max = $sValue;
				}

				$sStatName = preg_replace('/[^a-zA-Z0-9]/', '', $sStatName);
				$sClassName = "\\Stats\\$sStatName";

				/** @var \Stats\Base $oStat */
				$oStat = new $sClassName();
				$oStat->randomize((int)$min, (int)$max);
				$oMember->addStat($oStat);
			}

			if (isset($aData['skillsEnabled'])) {
				foreach ($aData['skillsEnabled'] as $sSkillName) {

					$sSkillName = preg_replace('/[^a-zA-Z0-9]/', '', $sSkillName);
					$nChance    = (int)$aData['skill'][$sSkillName];

					$sSkillType = $aData['skillType'][$sSkillName];
					$sSkillType = preg_replace('/[^a-zA-Z0-9]/', '', $sSkillType);

					$sClassName = "\\Skills\\$sSkillType\\$sSkillName";

					/** @var \Skills\Base $oSkill */
					$oSkill = new $sClassName();
					$oSkill->setChance($nChance);
					$oMember->addSkill($oSkill);
				}
			}

			$oBattle->attachMember($oMember);

		}

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

		$aRounds = BattleLog::getRounds();
		$aOriginalMembers = BattleLog::getOriginalMembers();
		$aEnd = BattleLog::getEnd();

		date_default_timezone_set("UTC");
		$sSaveTo = APP_PATH.'/battlelogs/'.date("YmdHis").'.json';
		if (is_writable(dirname($sSaveTo))) {
			$fp = fopen($sSaveTo, 'w');
			fwrite($fp, json_encode(array('post' => $post, 'rounds' => $aRounds, 'orim' => $aOriginalMembers, 'stillstand' => $aEnd)));
			fclose($fp);
		}else {
			throw new Exception("Cant write!");
		}


		$this->oView->assign('originalMembers', $aOriginalMembers);
		$this->oView->assign('aRounds', $aRounds);
		$this->oView->assign('stillstanding', $aEnd);

		$this->oView->display('battle');

	}


}
