<?php


/**
 * Class Autoloader
 */
class Autoloader
{
	/** @var string  $namespace   the namespace handled by this instance */
	protected $namespace = '';
	/** @var string  $path   where are the files located */
	protected $path = '';

	/** @var string  $nsSeparator */
	protected $nsSeparator = '\\';
	/** @var string  $fileExtension */
	protected $fileExtension = '.php';


	/**
	 * @param  string  $namespace  the namespace to be handled by this instance
	 * @param  string  $path       the path where the namespace files are located
	 */
	public function __construct($namespace, $path)
	{
		// Make the namespace always start with '\'
		$this->namespace = '\\'.trim($namespace, '\\');
		// Make the path always end with '/'
		$this->path      = rtrim(str_replace('\\', '/', $path), '/').'/';
	}



	/**
	 * Register the instance method 'loadClass' into the SPL's chain of autoloader handlers
	 *
	 * @return void
	 */
	public function register()
	{
		spl_autoload_register(array($this, 'loadClass'));
	}



	/**
	 * Unregister the instance method 'loadClass' from the SPL's chain of autoloader handlers
	 *
	 * @return void
	 */
	public function unregister()
	{
		spl_autoload_unregister(array($this, 'loadClass'));
	}



	/**
	 * @param  string  $className
	 * @return bool    there is no need to return something, SPL actually checks if the class exists after
	 *                 the autoloader ran and calls the next autoloader down the queue or stops searching
	 */
	public function loadClass($className)
	{
		// Make sure $className starts with '\\'
		$className = $this->nsSeparator.ltrim($className, $this->nsSeparator);

		// Attempt to split the provide class name into our namespace and name (maybe prepended with sub-namespaces)
		$prefix = rtrim($this->namespace, $this->nsSeparator).$this->nsSeparator;
		if (substr($className, 0, strlen($prefix)) === $prefix) {
			// Yes, it is in the namespace we handle
			// Strip the namespace from class name
			$rest = substr($className, strlen($prefix));

			// Split the class name into path and class name
			$classPath = '';
			$className = $rest;
			$pos = strripos($rest, $this->nsSeparator);

			if ($pos !== FALSE) {
				$classPath = substr($rest, 0, $pos + 1);
				$className = substr($rest, $pos + 1);
			}

			// Generate the filename
			$classPath = str_replace($this->nsSeparator, '/', $classPath);
			// PSR-0 compliant: $fileName  = str_replace('_', '/', $className).$this->fileExtension;
			$fileName  = $className.$this->fileExtension;

			$filePath = $this->path.$classPath.$fileName;

			if (file_exists($filePath)) {
				/** @noinspection PhpIncludeInspection */
				include $filePath;
				return TRUE;
			}
		}

		return FALSE;
	}
}
