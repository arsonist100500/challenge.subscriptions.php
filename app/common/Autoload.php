<?php

declare(strict_types=1);

namespace app\common;

if (defined('__Autoload__')) { return; }
define('__Autoload__', true);

require_once(__DIR__ . '/AppPath.php');
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
		$dirs = [self::getDocumentRoot(), __DIR__];
		$relativePath = str_replace('\\', '/', $className);
		foreach ($dirs as $directory) {
			$path = sprintf('%s/%s.php', $directory, $relativePath);
			Log::trace( sprintf('Autoload: class %s, path: %s', $className, $path) );
			if (is_readable($path) && include_once($path)) {
				Log::trace( sprintf('Autoload: class %s autoloaded', $className) );
				return;
			}
		}
		throw new UnknownClassException($className, sprintf('Autoload: failed to autoload class %s', $className));
	}

	protected static function getDocumentRoot() {
		return AppPath::ROOT;
	}
}

spl_autoload_extensions(join(',', ['.php', '.class.php', '.lib.php']));
spl_autoload_register('app\common\Autoloader::autoload');
