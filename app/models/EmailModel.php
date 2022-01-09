<?php

declare(strict_types=1);

namespace app\models;

/**
 * Class EmailModel
 * @package app\models
 * @property string $email
 * @property bool $checked
 * @property bool $valid
 */
class EmailModel extends AbstractModel {
    protected const TABLE = 'emails';
    protected const PRIMARY_KEY = 'email';
    protected const FIELD_TYPES = [
        'email' => FieldHelper::TYPE_STRING,
        'checked' => FieldHelper::TYPE_BOOL,
        'valid' => FieldHelper::TYPE_BOOL,
    ];
}
