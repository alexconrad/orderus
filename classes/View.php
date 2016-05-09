<?php

class View
{
	protected $aVars = [];
	
	protected $sPath;
	
	public function __construct($sPath)
	{
		$this->sPath = $sPath;
	}
	
	public function display($sFile)
	{
		$sFile = preg_replace('/[^a-zA-Z0-9]/', '', $sFile);

		extract($this->aVars);
		
		/** @noinspection PhpIncludeInspection */
		include $this->sPath.DIRECTORY_SEPARATOR.$sFile.'.php';
	}

	public function assign($sVar, $mValue)
	{
		$this->aVars[$sVar] = $mValue;
	}

}
