<?php

declare(strict_types=1);

namespace common;

use const app\ROOT_DIR;

if (defined('__Autoload__')) { return; }
define('__Autoload__', true);

require_once(__DIR__ . '/../config/app.php');
require_once(__DIR__ . '/Log.php');

class UnknownClassException extends \Exception {
	protected $sClassName = '';		// Full class name.

	public function __construct($sClassName, $sMessage) {
		parent::__construct((string)$sMessage);
		$this->sClassName = $sClassName;
	}

	public function getName() {
		return $this->sClassName;
	}
}

class Autoloader {

    /**
     * @param string $className
     * @throws UnknownClassException
     */
	public static function autoload(string $className): void {
		// Set directories to search in.
		$dirs = [self::getDocumentRoot(), dirname(__FILE__), self::getVendorDirectory()];
		// Get relative path.
		$relativePath = str_replace('\\', '/', $className);
		// Iterate over directories.
		foreach ($dirs as $directory) {
			// Set absolute path.
			$path = sprintf('%s/%s.php', $directory, $relativePath);
			// Logging.
			Log::trace( sprintf('Autoload: class %s, path: %s', $className, $path) );
			// Try to include file.
			if (is_readable($path) && include_once($path)) {
				Log::trace( sprintf('Autoload: class %s autoloaded', $className) );
				return;
			}
		}

		// Unable to auto-load class. Throw exception.
		throw new UnknownClassException($className, sprintf('Autoload: failed to autoload class %s', $className));
	}

	protected static function getDocumentRoot() {
		return ROOT_DIR;
	}

	protected static function getVendorDirectory() {
		return dirname(__FILE__).'/vendor';
	}
}

spl_autoload_extensions(join(',', ['.php', '.class.php', '.lib.php']));
spl_autoload_register('common\Autoloader::autoload');
