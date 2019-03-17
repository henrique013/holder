<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 09/03/2018
 * Time: 11:39
 */

namespace Holder\Util\DB;

use PDO;


abstract class DB extends PDO
{
    private $nullValues = [null];


    public function setNullValues(array $nullValues): void
    {
        $this->nullValues = $nullValues;
    }

    public function getNullValues(): array
    {
        return $this->nullValues;
    }

    public function isNullValue($value): bool
    {
        return in_array($value, $this->nullValues, true);
    }

    protected function mountWhereStatement(array $whereVals): string
    {
        $where = [];


        foreach ($whereVals as $col => $val)
        {
            $col = $this->escape($col);
            $val = $this->isNullValue($val) ? 'IS NULL' : ' = ' . $this->quote($val);


            $where[] = "{$col} {$val}";
        }


        $where = implode("\r\n" . 'AND ', $where);


        return $where;
    }

    protected function mountUpdateStatement(array $setVals): string
    {
        $update = [];


        foreach ($setVals as $col => $val)
        {
            $col = $this->escape($col);
            $val = $this->isNullValue($val) ? 'NULL' : $this->quote($val);


            $update[] = "{$col} = {$val}";
        }


        $update = implode("\r\n,", $update);


        return $update;
    }

    /**
     * @param string $name Ex: "col_1"
     * @return string      Ex: "`col_1`"
     */
    public abstract function escape(string $name): string;

    /**
     * @param string $table Ex: "tb_teste"
     * @param array $rows   Ex: [["coluna1" => 10, "coluna2" => "foo", "coluna3" => null]]
     * @return int             Affected rows
     */
    public abstract function insert(string $table, array $rows): int;

    /**
     * @param string $table    Ex: "tb_teste"
     * @param array $setVals   Ex: ["coluna1" => 10, "coluna2" => "foo", "coluna3" => null]
     * @param array $whereVals Ex: ["id" => 1]
     * @return int             Affected rows
     */
    public abstract function update(string $table, array $setVals, array $whereVals): int;

    /**
     * @param string $table    Ex: "tb_teste"
     * @param array $whereVals Ex: ["id" => 1]
     * @return int             Affected rows
     */
    public abstract function delete(string $table, array $whereVals): int;
}