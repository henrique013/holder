<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 09/03/2018
 * Time: 11:41
 */

namespace Holder\Util\DB;


class Postgre extends DB
{
    const ON_CONFLICT_NOTHING = 'nothing';
    const ON_CONFLICT_UPDATE = 'update';


    public function insert(string $table, array $rows): int
    {
        return $this->_insert($table, $rows);
    }

    /**
     * @param string $table          Ex: "tb_teste"
     * @param array $rows            Ex: [["coluna1" => 10, "coluna2" => "foo", "coluna3" => null]]
     * @param array $conflictColumns Ex: ["coluna1", "coluna2"]
     * @return int                   Affected rows
     */
    public function insertIgnore(string $table, array $rows, array $conflictColumns): int
    {
        return $this->_insert($table, $rows, self::ON_CONFLICT_NOTHING, $conflictColumns);
    }

    /**
     * @param string $table          Ex: "tb_teste"
     * @param array $rows            Ex: [["coluna1" => 10, "coluna2" => "foo", "coluna3" => null]]
     * @param array $conflictColumns Ex: ["coluna1", "coluna2"]
     * @return int                   Affected rows
     */
    public function upsert(string $table, array $rows, array $conflictColumns): int
    {
        return $this->_insert($table, $rows, self::ON_CONFLICT_UPDATE, $conflictColumns);
    }

    public function update(string $table, array $setVals, array $whereVals): int
    {
        $table = $this->escape($table);
        $update = $this->mountUpdateStatement($setVals);
        $where = $this->mountWhereStatement($whereVals);


        $sql = "
            UPDATE {$table}
            SET
                {$update}
            WHERE
                {$where}
        ";
        $stmt = $this->prepare($sql);
        $stmt->execute();
        $ret = $stmt->rowCount();


        return $ret;
    }

    public function delete(string $table, array $whereVals): int
    {
        $table = $this->escape($table);
        $where = $this->mountWhereStatement($whereVals);


        $sql = "
            DELETE FROM {$table}
            WHERE
                {$where}
        ";
        $stmt = $this->prepare($sql);
        $stmt->execute();
        $ret = $stmt->rowCount();


        return $ret;
    }

    public function escape(string $name): string
    {
        return '"' . $name . '"';
    }

    private function _insert(string $table, array $rows, string $onConflict = null, array $conflictColumns = []): int
    {
        $cols = array_map([$this, 'escape'], array_keys($rows[0]));
        $conflictColumns = array_map([$this, 'escape'], $conflictColumns);
        $values = [];
        $conflictSQL = '';


        switch ($onConflict)
        {
            case self::ON_CONFLICT_NOTHING:

                $conflictColumns = implode(',', $conflictColumns);

                $conflictSQL = "ON CONFLICT ({$conflictColumns}) DO NOTHING";

                break;

            case self::ON_CONFLICT_UPDATE:

                $conflictValues = [];

                foreach ($cols as $col)
                    $conflictValues[] = "{$col} = excluded.{$col}";

                $conflictColumns = implode(',', $conflictColumns);
                $conflictValues = implode("\r\n,", $conflictValues);

                $conflictSQL = "
                    ON CONFLICT ({$conflictColumns}) DO UPDATE SET
                    {$conflictValues}
                ";

                break;
        }


        foreach ($rows as $row)
        {
            $vals = [];


            foreach ($row as $col => $val)
            {
                $vals[$col] = $this->isNullValue($val) ? 'NULL' : $this->quote($val);
            }


            $values[] = '(' . implode(',', $vals) . ')';
        }


        $cols = implode(',', $cols);
        $values = implode("\r\n,", $values);
        $table = $this->escape($table);


        $sql = "
            INSERT INTO {$table}
            ({$cols})
            VALUES
            {$values}
            {$conflictSQL}
        ";
        $stmt = $this->prepare($sql);
        $stmt->execute();
        $ret = $stmt->rowCount();


        return $ret;
    }
}