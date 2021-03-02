<?php

class BasicModel
{
    private $dbase;

    public function __construct()
    {
        $this->dbase = new Database;
    }

    public function getThisQuery($sql)
    {
        $this->dbase->Query($sql);
        return $this->dbase->resultSet();
    }

    public function getTable($table)
    {
        $query = "SELECT * FROM $table";
        $this->dbase->Query($query);
        return $this->dbase->resultSet();
    }

    public function getWhere($table, $field, $where, $like = false)
    {
        if ($like) {
            $query = "SELECT * FROM $table WHERE $field LIKE :field";
            $this->dbase->Query($query);
            $this->dbase->bind('field', $where);
            return $this->dbase->resultSet();
        } else {
            $query = "SELECT * FROM $table WHERE $field = :field";
            $this->dbase->Query($query);
            $this->dbase->bind('field', $where);
            return $this->dbase->singleResult();
        }
    }

    public function deleteRecord($table, $field, $where)
    {
        $query = "DELETE FROM $table WHERE $field = :field";
        $this->dbase->Query($query);
        $this->dbase->bind('field', $where);
        $this->dbase->executeQuery();
        return ($this->dbase->countRows() > 0) ? true : false;
    }

    public function insertTable($table, $data = [])
    {
        $query = 'INSERT INTO ' . $table . ' (' . implode(', ', array_keys($data)) . ') VALUES (' . implode(', ', preg_filter('/^/', ':', array_keys($data))) . ')';
        $this->dbase->Query($query);
        foreach ($data as $key => $val) {
            $this->dbase->bind($key, $val);
        }
        $this->dbase->executeQuery();
        return ($this->dbase->countRows() > 0) ? true : false;
    }

    public function updateRecord($table, $data = [], $where = [])
    {
        $query = 'UPDATE ' . $table . ' SET ' . implode(', ', preg_filter('/.+/', '$0 = :$0', array_keys($data))) . ' WHERE ' . implode(' AND ', preg_filter('/.+/', '$0 = :$0', array_keys($where)));
        $this->dbase->Query($query);
        foreach ($data as $key => $val) {
            $this->dbase->bind($key, $val);
        }
        foreach ($where as $key => $val) {
            $this->dbase->bind($key, $val);
        }
        $this->dbase->executeQuery();
        return ($this->dbase->countRows() > 0) ? true : false;
    }

    public function recordCount($query)
    {
        $this->dbase->Query($query);
        $this->dbase->executeQuery();
        return $this->dbase->countRows();
    }
}
