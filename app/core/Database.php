<?php

class Database
{
    private $host = DBS_HOST;
    private $user = DBS_USER;
    private $pass = DBS_PASS;
    private $dbase = DBS_NAME;

    private $dbHandler, $statement;

    public function __construct()
    {
        $dataSource = 'mysql:host=' . $this->host . ';dbname=' . $this->dbase;
        $option = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];
        try {
            $this->dbHandler = new PDO($dataSource, $this->user, $this->pass, $option);
        } catch (PDOException $Ex) {
            die($Ex->getMessage());
        }
    }

    public function getAll($sql)
    {
        $this->statement = $this->dbHandler->prepare($sql);
        $this->statement->execute();
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function Query($query)
    {
        $this->statement = $this->dbHandler->prepare($query);
    }

    public function bind($param, $vals, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($vals):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($vals):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($vals):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    break;
            }
        }
        $this->statement->bindValue($param, $vals, $type);
    }

    public function executeQuery()
    {
        $this->statement->execute();
    }

    public function resultSet()
    {
        $this->executeQuery();
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function singleResult()
    {
        $this->executeQuery();
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }

    public function countRows()
    {
        return $this->statement->rowCount();
    }
}
