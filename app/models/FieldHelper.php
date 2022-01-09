<?php

declare(strict_types=1);

namespace app\models;

class FieldHelper {
    public const TYPE_INT = 'int';
    public const TYPE_STRING = 'string';
    public const TYPE_BOOL = 'bool';
    public const TYPE_JSON = 'json';
    public const TYPES = [
        self::TYPE_INT => self::TYPE_INT,
        self::TYPE_STRING => self::TYPE_STRING,
        self::TYPE_BOOL => self::TYPE_BOOL,
        self::TYPE_JSON => self::TYPE_JSON,
    ];

    public static function cast($value, string $type) {
        if (isset(self::TYPES[$type])) {
            switch ($type) {
                case self::TYPE_INT: return (int)$value;
                case self::TYPE_STRING: return (string)$value;
                case self::TYPE_BOOL: return (bool)$value;
                case self::TYPE_JSON:
                    return \is_array($value)
                        ? $value
                        : (\is_string($value) ? \json_decode($value) : $value);
                default: break;
            }
        }
        return $value;
    }

    public static function toIntOrString($value, string $type) {
        if (isset(self::TYPES[$type])) {
            switch ($type) {
                case self::TYPE_INT: return (int)$value;
                case self::TYPE_STRING: return (string)$value;
                case self::TYPE_BOOL: return $value ? 1 : 0;
                case self::TYPE_JSON:
                    return \is_string($value)
                        ? $value
                        : (\is_array($value) ? \json_encode($value) : $value);
                default: break;
            }
        }
        return $value;
    }
}
