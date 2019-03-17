<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 09/03/2018
 * Time: 11:41
 */

namespace Holder\Util\DB;


class MySQL extends DB
{
    const ON_DUPLICATE_IGNORE = 'ignore';
    const ON_DUPLICATE_UPDATE = 'update';


    public function insert(string $table, array $rows): int
    {
        return $this->_insert($table, $rows);
    }

    /**
     * @param string $table Ex: "tb_teste"
     * @param array $rows   Ex: [["coluna1" => 10, "coluna2" => "foo", "coluna3" => null]]
     * @return int          Affected rows
     */
    public function insertIgnore(string $table, array $rows): int
    {
        return $this->_insert($table, $rows, self::ON_DUPLICATE_IGNORE);
    }

    /**
     * @param string $table Ex: "tb_teste"
     * @param array $rows   Ex: [["coluna1" => 10, "coluna2" => "foo", "coluna3" => null]]
     * @return int          Affected rows
     */
    public function upsert(string $table, array $rows): int
    {
        return $this->_insert($table, $rows, self::ON_DUPLICATE_UPDATE);
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
        return '`' . $name . '`';
    }

    private function _insert(string $table, array $rows, string $onDuplicate = null): int
    {
        $cols = array_map([$this, 'escape'], array_keys($rows[0]));
        $values = [];
        $tipo = '';
        $conflictSQL = '';


        switch ($onDuplicate)
        {
            case self::ON_DUPLICATE_IGNORE:

                $tipo = 'IGNORE';

                break;

            case self::ON_DUPLICATE_UPDATE:

                $updateValues = [];

                foreach ($cols as $col)
                {
                    $updateValues[] = "{$col} = VALUES({$col})";
                }

                $updateValues = implode("\r\n,", $updateValues);

                $conflictSQL = "
                    ON DUPLICATE KEY UPDATE
                    {$updateValues}
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
            INSERT {$tipo} INTO {$table}
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