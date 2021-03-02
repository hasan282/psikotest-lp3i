<?php

class PesertaModel
{
    private $dbase;

    public function __construct()
    {
        $this->dbase = new Database;
    }

    public function getList($record, $rows, $filter = [])
    {
        if (sizeof($filter) > 0) {
            $this->_setQuery('peserta.id, nama, sch.sekolah, q_number, karakter.karakter', $filter, ' ORDER BY peserta.id DESC LIMIT ' . $record . ', ' . $rows);
        } else {
            $query = $this->_query('peserta.id, nama, sch.sekolah, q_number, karakter.karakter', 'ORDER BY peserta.id DESC LIMIT ' . $record . ', ' . $rows);
            $this->dbase->Query($query);
        }
        return $this->dbase->resultSet();
    }

    public function getOne($id)
    {
        $query = $this->_query('peserta.id, nama, kelamin, telpon, sch.sekolah, jurusan, answers, q_number, karakter.karakter', 'WHERE peserta.id = :pid');
        $this->dbase->Query($query);
        $this->dbase->bind('pid', $id);
        return $this->dbase->singleResult();
    }

    private function _query($column, $condition = null)
    {
        $condition = ($condition === null) ? '' : ' ' . $condition;
        return "SELECT $column FROM ((peserta LEFT JOIN (SELECT id, sekolah FROM sekolah UNION SELECT * FROM sekolah_temp) AS sch ON sch.id = peserta.sekolah) INNER JOIN hasil ON hasil.peserta_id = peserta.id) LEFT JOIN karakter ON hasil.karakter = karakter.id" . $condition;
    }

    public function getTotal($filter = [])
    {
        if (sizeof($filter) > 0) {
            $this->_setQuery('peserta.id', $filter);
        } else {
            $query = $this->_query('peserta.id');
            $this->dbase->Query($query);
        }
        $this->dbase->executeQuery();
        return $this->dbase->countRows();
    }

    private function _setQuery($column, $filter, $condition = '')
    {
        $Where = array();
        if (isset($filter['nama'])) array_push($Where, 'nama LIKE :nama');
        if (isset($filter['kelamin'])) array_push($Where, 'kelamin = :kelamin');
        if (isset($filter['sekolah'])) array_push($Where, 'sch.id = :sekolah');
        if (isset($filter['proses'])) array_push($Where, 'q_number ' . $filter['proses']);
        $query = $this->_query($column, 'WHERE ' . implode(' AND ', $Where) . $condition);
        $this->dbase->Query($query);
        if (isset($filter['nama'])) $this->dbase->bind('nama', '%' . $filter['nama'] . '%');
        if (isset($filter['kelamin'])) $this->dbase->bind('kelamin', $filter['kelamin']);
        if (isset($filter['sekolah'])) $this->dbase->bind('sekolah', $filter['sekolah']);
    }

    public function getExportData($column = [], $limit, $filter = [])
    {
        $ColNames = array(
            'nama' => 'nama', 'kelamin' => 'kelamin', 'telpon' => 'telpon', 'sekolah' => 'sch.sekolah',
            'jurusan' => 'jurusan', 'tanggal' => 'peserta.id', 'status' => 'q_number', 'karakter' => 'karakter.karakter'
        );
        foreach (array_keys($ColNames) as $key) {
            if (!in_array($key, $column)) unset($ColNames[$key]);
        }
        if (sizeof($filter) > 0) {
            $this->_setQuery(implode(', ', array_values($ColNames)), $filter, ' ORDER BY peserta.id DESC LIMIT ' . $limit);
        } else {
            $Query = $this->_query(implode(', ', array_values($ColNames)), 'ORDER BY peserta.id DESC LIMIT ' . $limit);
            $this->dbase->Query($Query);
        }
        return $this->dbase->resultSet();
    }
}
