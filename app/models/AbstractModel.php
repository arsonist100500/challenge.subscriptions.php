<?php

declare(strict_types=1);

namespace app\models;

use app\database\PDOHelper;

abstract class AbstractModel {
    /** @var string Database table name */
    protected const TABLE = '';
    /** @var string Column used as primary key */
    protected const PRIMARY_KEY = '';
    /** @var string[] Fields configuration: field name => type */
    protected const FIELD_TYPES = [];

    /** @var array<string,mixed> Model attributes */
    protected $values = [];
    /** @var array<string,bool> Array of changed attributes */
    protected $changed = [];

    /**
     * AbstractModel constructor.
     * @param array $row
     */
    function __construct(array $row = []) {
        $this->values = \array_intersect_key($row, static::FIELD_TYPES);
        $this->castValues();
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name) {
        return $this->values[$name] ?? null;
    }

    /**
     * @param string $name
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    public function __set(string $name, $value) {
        $type = static::FIELD_TYPES[$name] ?? null;
        if ($type !== null) {
            $this->changed[$name] = true;
            $this->values[$name] = FieldHelper::cast($value, $type);
            return $value;
        }
        throw new \Exception('invalid attribute: ' . $name);
    }

    /**
     * @param array $where
     * @return $this|null
     * @throws \Exception
     */
    public static function get(array $where): ?self {
        $row = PDOHelper::get(static::TABLE, $where);
        return $row ? new static($row) : null;
    }

    /**
     * @return mixed|null
     * @throws \Exception
     */
    public function insert() {
        $pk = PDOHelper::insert(static::TABLE, $this->prepareValuesForDatabase(), $error);
        if ($pk !== false) {
            $this->values[static::PRIMARY_KEY] = FieldHelper::cast($pk, static::FIELD_TYPES[static::PRIMARY_KEY]);
            return $pk;
        }
        throw new \Exception('failed to insert: ' . $error);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function update(): bool {
        $pk = $this->getPrimaryKey();
        if ($pk) {
            $result = PDOHelper::update(static::TABLE, $this->getChangedAttributes(), $pk);
            $this->changed = [];
            return $result;
        }
        return false;
    }

    /**
     * @return array|null
     */
    protected function getPrimaryKey(): ?array {
        if (!isset($this->values[static::PRIMARY_KEY])) {
            return null;
        }
        return \array_intersect_key($this->values, [static::PRIMARY_KEY => 1]);
    }

    /**
     * @return array
     */
    protected function getChangedAttributes(): array {
        return \array_intersect_key($this->prepareValuesForDatabase(), $this->changed);
    }

    protected function castValues(): void {
        foreach ($this->values as $key => $value) {
            $type = static::FIELD_TYPES[$key] ?? FieldHelper::TYPE_STRING;
            $this->values[$key] = FieldHelper::cast($value, $type);
        }
    }

    /**
     * @return array
     */
    protected function prepareValuesForDatabase(): array {
        $result = [];
        foreach ($this->values as $key => $value) {
            $type = static::FIELD_TYPES[$key] ?? FieldHelper::TYPE_STRING;
            $result[$key] = FieldHelper::toIntOrString($value, $type);
        }
        return $result;
    }
}
