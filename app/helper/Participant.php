<?php

class Participant extends Controller
{
    public function setData($encryp = null)
    {
        if ($encryp === null) {
            $PARTIDAT = (isset($_COOKIE['PARTIDAT'])) ? $_COOKIE['PARTIDAT'] : '00000000';
        } else {
            $PARTIDAT = $encryp;
        }
        if ($PARTIDAT != '00000000') {
            $DataPeserta = $this->model()->getThisQuery("SELECT peserta.id, nama, sch.sekolah, q_number FROM (peserta LEFT JOIN (SELECT id, sekolah FROM sekolah UNION SELECT * FROM sekolah_temp) AS sch ON peserta.sekolah = sch.id) INNER JOIN hasil ON peserta.id = hasil.peserta_id WHERE hasil.encrypted = '$PARTIDAT'");
            if ($DataPeserta) {
                $this->session('peserta')->set(array(
                    'id' => $DataPeserta[0]['id'], 'nama' => $DataPeserta[0]['nama'],
                    'sekolah' => strtoupper($DataPeserta[0]['sekolah']), 'number' => $DataPeserta[0]['q_number']
                ));
            }
        }
    }

    public function getData($id = null, $page = 1, $list = 10, $filter = [])
    {
        $Result = false;
        if ($id === null) {
            $MaxQuestion = $this->model()->recordCount('SELECT SUBSTRING(id,1,3) AS ids FROM question GROUP BY ids');
            $FilterData = $filter;
            if (isset($filter['proses'])) {
                $QueryProses = ($filter['proses']) ? '= ' . $MaxQuestion : '< ' . $MaxQuestion;
                $FilterData['proses'] = $QueryProses;
            }
            $TotalData = (sizeof($filter) > 0) ? $this->model('PesertaModel')->getTotal($FilterData) : $this->model()->recordCount('SELECT id FROM peserta');
            $TotalPage = ($TotalData % $list == 0) ? ($TotalData / $list) : (floor($TotalData / $list) + 1);
            if ($page > 0 && $page <= $TotalPage) {
                $DataPeserta = $this->model('PesertaModel')->getList((($page - 1) * $list), $list, $FilterData);
                $data = array();
                for ($dp = 0; $dp < sizeof($DataPeserta); $dp++) {
                    $data[$dp]['id'] = $DataPeserta[$dp]['id'];
                    $data[$dp]['tanggal'] = $this->helper('Date')->toDate($DataPeserta[$dp]['id'], 21);
                    $data[$dp]['nama'] = $DataPeserta[$dp]['nama'];
                    $data[$dp]['sekolah'] = $DataPeserta[$dp]['sekolah'];
                    if ($DataPeserta[$dp]['q_number'] == $MaxQuestion) {
                        $data[$dp]['proses'] = 'Selesai';
                    } else {
                        $data[$dp]['proses'] = $DataPeserta[$dp]['q_number'] . ' / ' . $MaxQuestion;
                    }
                    $data[$dp]['karakter'] = ($DataPeserta[$dp]['karakter'] == null) ? '-' : $DataPeserta[$dp]['karakter'];
                }
                $Result = array('data' => $data, 'total' => $TotalData);
            }
        } else {
            $Peserta = $this->model('PesertaModel')->getOne($id);
            if ($Peserta) {
                $MaxQuestion = $this->model()->recordCount('SELECT SUBSTRING(id,1,3) AS ids FROM question GROUP BY ids');
                $Answer = $this->_answers($Peserta['answers']);
                $data = array(
                    'id' => $Peserta['id'], 'nama' => $Peserta['nama'], 'kelamin' => $Peserta['kelamin'],
                    'telpon' => $Peserta['telpon'], 'sekolah' => $Peserta['sekolah'], 'jurusan' => $Peserta['jurusan'],
                    'tanggal' => $this->helper('Date')->toDate($Peserta['id'], 20),
                    'proses' => $Peserta['q_number'] . '/' . $MaxQuestion, 'karakter' => $Peserta['karakter'],
                    'answer_a' => $Answer['A'], 'answer_b' => $Answer['B'], 'answer_c' => $Answer['C'], 'answer_d' => $Answer['D']
                );
                $Result = array('data' => $data, 'total' => 1);
            }
        }
        return $Result;
    }

    private function _answers($answers)
    {
        $count = array('A' => 0, 'B' => 0, 'C' => 0, 'D' => 0);
        $answer = explode(',', $answers);
        foreach ($answer as $ans) {
            $count[$ans] += 1;
        }
        return $count;
    }
}
