<?php

class SearchModel
{
    private $dbase;

    public function __construct()
    {
        $this->dbase = new Database;
    }

    public function result($data)
    {
        $where = array();
        $whereVal = array();
        if (isset($data['nm']) && $data['nm'] != '') {
            $whereVal['nama'] = '%' . $data['nm'] . '%';
            array_push($where, 'nama LIKE :nama');
        }
        if (isset($data['sch']) && $data['sch'] != '') {
            $whereVal['sekolah'] = $data['sch'];
            array_push($where, 'sekolah = :sekolah');
        }
        if (isset($data['tgl']) && $data['tgl'] != '') {
            $whereVal['id'] = substr(implode('', array_reverse(explode('-', $data['tgl']))), 2) . '%';
            array_push($where, 'id LIKE :id');
        }
        $query = 'SELECT id, encrypted, nama, q_number AS num, jml FROM (SELECT * FROM peserta INNER JOIN hasil ON peserta.id = hasil.peserta_id) AS psr, (SELECT COUNT(ids) AS jml FROM (SELECT SUBSTRING(id,1,3) AS ids FROM question GROUP BY ids) AS qs) AS tot WHERE ' . implode(' AND ', $where) . ' ORDER BY id DESC LIMIT 10';
        $this->dbase->Query($query);
        foreach ($whereVal as $key => $val) {
            $this->dbase->bind($key, $val);
        }
        return $this->dbase->resultSet();
    }

    public function srcContinue($enc)
    {
        $query = 'SELECT id, encrypted, nama, telpon, q_number FROM peserta INNER JOIN hasil ON peserta.id = hasil.peserta_id WHERE encrypted = :encryp';
        $this->dbase->Query($query);
        $this->dbase->bind('encryp', $enc);
        return $this->dbase->singleResult();
    }
}
