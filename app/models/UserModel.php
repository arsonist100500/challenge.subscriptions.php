<?php

declare(strict_types=1);

namespace app\models;

/**
 * Class UserModel
 * @package app\models
 * @property int $id
 * @property string $username
 * @property string $email
 * @property int $validts
 * @property bool $confirmed
 * @property string $notified
 */
class UserModel extends AbstractModel {
    protected const TABLE = 'users';
    protected const PRIMARY_KEY = 'id';
    protected const FIELD_TYPES = [
        'id' => FieldHelper::TYPE_INT,
        'username' => FieldHelper::TYPE_STRING,
        'email' => FieldHelper::TYPE_STRING,
        'validts' => FieldHelper::TYPE_INT,
        'confirmed' => FieldHelper::TYPE_BOOL,
        'notified' => FieldHelper::TYPE_STRING,
    ];
}
