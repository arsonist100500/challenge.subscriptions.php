<?php

declare(strict_types=1);

namespace app\common;

class Log {
	// Verbosity levels
	const TRACE		= 7;
	const DEBUG		= 6;
	const INFO		= 5;
	const NOTICE	= 4;
	const WARNING	= 3;
	const ERROR		= 2;
	const FATAL		= 1;

	protected static $flagPrintVerbosity = false;
	protected static $flagPrefixMultiline = false;
	protected static $flagArrayPrettyPrint = false;
	protected static $errors = [];
	protected static $buffer = [];
	protected static $verbosity = self::INFO;
	protected static $prefix = "";
	protected static $fileName = null;
	protected static $instanceAutoSave = null;		// Object needed to auto-save log file.

    /**
     * @throws \Exception
     */
	function __destruct() {
		if (self::$instanceAutoSave instanceof Log) {
			self::saveMessagesIntoFile();
			self::$instanceAutoSave = null;
		}
	}

    /**
     * @param string $s
     * @throws \Exception
     */
	public static function setLogFileName(string $s) {
		if (self::isFileWriteReady($s)) {
			self::$fileName = $s;
			// Create object to allow log auto-saving.
			self::$instanceAutoSave = new self();
		} else {
            throw new \Exception(sprintf("file is not writable: '%s'", $s));
        }
	}

	public static function setVerbosity (int $value) { self::$verbosity = $value; }
	public static function setFlagPrintVerbosity(bool $value) { self::$flagPrintVerbosity = $value; }
	public static function setFlagPrefixMultiline(bool $value) { self::$flagPrefixMultiline = $value; }
	public static function setFlagArrayPrettyPrint(bool $value) { self::$flagArrayPrettyPrint = $value; }
	public static function setMessagePrefix(string $s) { self::$prefix = $s; self::trimMessagePrefix(); }
	public static function appendMessagePrefix(string $s) { self::$prefix .= "-".$s; self::trimMessagePrefix(); }

	public static function trace($s) { self::addMessage($s, self::TRACE); }
	public static function debug($s) { self::addMessage($s, self::DEBUG); }
	public static function notice($s) { self::addMessage($s, self::NOTICE); }
	public static function info($s) { self::addMessage($s, self::INFO); }
	public static function warning($s) { self::addMessage("[WARNING] ".$s, self::WARNING); }
	public static function error($s) {
		$s = "[ERROR] ".$s;
		self::$errors[] = self::makeLogLine($s);
		self::addMessage($s, self::ERROR);
	}

	public static function getMessages(): array {
		if (\is_array(self::$buffer)) {
			$a = [];
			$prefix = (empty(self::$prefix) ? "" : self::$prefix.": ");
			$verbosityMapping = [
				self::TRACE		=> "trace",
				self::DEBUG		=> "debug",
				self::INFO		=> "info",
				self::NOTICE	=> "notice",
				self::WARNING	=> "WARNING",
				self::ERROR		=> "ERROR",
				self::FATAL		=> "FATAL ERROR",
			];
			foreach(self::$buffer as $x) {
				if ($x["verbosity"] <= self::$verbosity) {
					$verbosity = (self::$flagPrintVerbosity ? sprintf("[%7s]\t", $verbosityMapping[$x["verbosity"]]) : "");
					$a[] = $verbosity.$prefix.$x["line"];
				}
			}
			return $a;
		}
		return [];
	}

    private static function makeLogLine($s, $uOffsetHours = 3) {
		$uTimestamp = time() + $uOffsetHours * 3600;
		list($usec) = explode(" ", microtime());
		$usec = round($usec * 1000);
		return sprintf("[%s.%03d UTC%+d] %s", gmdate("Y-m-d H:i:s", $uTimestamp), $usec, $uOffsetHours, $s);
	}

    private static function trimMessagePrefix() {
		self::$prefix = preg_replace('/[: ]+$/', '', self::$prefix);
	}

    private static function addMessage($s, $verbosity) {
		if ($verbosity <= self::$verbosity) {
			$a = \explode("\n", $s);
			foreach ($a as $line) {
				$line = self::makeLogLine($line);
				self::$buffer[] = ["line" => $line, "verbosity" => $verbosity];
			}
		}
	}

    private static function isFileWriteReady(string $s): bool {
        return (
            (!\file_exists($s) and \is_dir(dirname($s)) and \is_writable(dirname($s)))
            or (\is_writable($s) and (\is_file($s) or \is_link($s)))
        );
    }

    /**
     * @throws \Exception
     */
    private static function saveMessagesIntoFile() {
		if (self::$fileName) {
			$lines = self::getMessages();
			if (!empty($lines)) {
				$sBuffer = join("\n", $lines);
				$sBuffer .= "\n\n\n\n\n";
				$bSaved = file_put_contents(self::$fileName, $sBuffer, FILE_APPEND | LOCK_EX);
				// Set permissions: group has write-permission.
				if ($bSaved) { @chmod(self::$fileName, 0664); }
				// Throw exception if log was not saved.
				if (!$bSaved) {
					throw new \Exception(sprintf("%s: %s(): Unable to save log file", __CLASS__, __FUNCTION__));
				}
			} else {
				self::debug(sprintf("%s: %s(): log is empty, nothing to save", __CLASS__, __FUNCTION__));
			}
		}
	}
}
