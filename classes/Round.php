<?php

class Round
{
	/** @var  array */
	protected $aMemberOrder;

	/** @var  Turn */
	protected $oTurn;

	/** @var  Member[] */
	protected $aMembers;

	/**
	 * Turn constructor.
	 * @param Member[] $aMembers
	 */
	public function __construct($aMembers)
	{
		$this->aMembers = $aMembers;
	}


	/**
	 * Makes the order in which the members attack.
	 */
	public function roundOrder()
	{
		$aOrder = [];
		foreach ($this->aMembers as $sMemberIndex => $oMember) {
			if ($oMember->isAlive()) {
				$aOrder[] = array(
					'index' => $sMemberIndex,
					'speed' => $this->aMembers[$sMemberIndex]->getStat(Stats\Speed::class)->getAmount(),
					'luck' => $this->aMembers[$sMemberIndex]->getStat(Stats\Luck::class)->getAmount(),
				);
			}
		}

		usort($aOrder, function ($aFirst, $aSecond) {
			if ($aFirst['speed'] == $aSecond['speed']) {
				if ($aFirst['luck'] == $aSecond['luck']) {
					return rand(-10,10);
				}else {
					return -1 * ($aFirst['luck'] - $aSecond['luck']);
				}
			}else {
				return -1 * ($aFirst['speed'] - $aSecond['speed']);
			}
		});

		$this->aMemberOrder = array_column($aOrder, 'index');
	}

	/**
	 * @return string
	 *
	 * @throws Exception
	 */
	public function getNextAttackingMember() {

		$sMemberIndex = array_shift($this->aMemberOrder);

		$nCheck3InfiniteLoop = 0;
		while (!is_null($sMemberIndex)) {

			$nCheck3InfiniteLoop++;
			if ($nCheck3InfiniteLoop > 5000) {
				throw new \Exception("Likely infinite loop: ".__FILE__.":".__LINE__);
			}

			if ($this->aMembers[$sMemberIndex]->isAlive()) {
				return $sMemberIndex;
			}

			$sMemberIndex = array_shift($this->aMemberOrder);

		}

		return FALSE;
	}

	/**
	 * @param $sAttackingIndex
	 * @param $aMembers
	 * @return Turn
	 */
	public function newTurn($sAttackingIndex, $aMembers)
	{
		$this->oTurn = new Turn($sAttackingIndex, $aMembers);
		return $this->oTurn;
	}


	public function getOrder()
	{
		return $this->aMemberOrder;
	}


}
