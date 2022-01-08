<?php

declare(strict_types=1);

namespace app\common;

class Config {
    protected static $configData = null;

    public static function get(string $name) {
        if (self::$configData === null) {
            self::$configData = self::readFiles();
        }
        $keys = \explode('.', $name);
        if (!empty($keys)) {
            return self::getValueByPath(self::$configData, $keys);
        }
        return null;
    }

    /**
     * @return array
     */
    protected static function readFiles(): array {
        $files = [];
        $iterator = new \DirectoryIterator(AppPath::CONFIG);
        foreach ($iterator as $file) {
            if (!$file->isDot() && $file->isFile() && $file->isReadable()) {
                $name = $file->getBasename('.'.$file->getExtension());
                $files[$name] = include($file->getRealPath());
                Log::debug('found config: ' . $name);
            }
        }
        return $files;
    }

    /**
     * @param array $data
     * @param array $keys
     * @return mixed|null
     */
    protected static function getValueByPath(array $data, array $keys) {
        if (empty($data)) {
            return null;
        }
        $key = array_shift($keys);
        if (empty($keys)) {
            return $data[$key] ?? null;
        }
        return self::getValueByPath($data[$key] ?? [], $keys);
    }
}
