<?php

declare(strict_types=1);

namespace app\models;

/**
 * Class TaskModel
 * @package app\models
 * @property int $id
 * @property string $username
 * @property string $started
 * @property string $finished
 * @property array $result
 */
class TaskModel extends AbstractModel {
    protected const TABLE = 'tasks';
    protected const PRIMARY_KEY = 'id';
    protected const FIELD_TYPES = [
        'id' => FieldHelper::TYPE_INT,
        'username' => FieldHelper::TYPE_STRING,
        'started' => FieldHelper::TYPE_STRING,
        'finished' => FieldHelper::TYPE_STRING,
        'result' => FieldHelper::TYPE_JSON,
    ];
}
