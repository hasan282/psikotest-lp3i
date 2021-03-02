<?php

class parti extends Controller
{
    public function index()
    {
        Redirect();
    }

    public function add()
    {
        if ($_POST && isset($_REQUEST['nama'])) {
            $PesertaID = date('ymdHis');
            while ($this->model()->getWhere('peserta', 'id', $PesertaID)) {
                $PesertaID = strval($PesertaID + 1);
            }
            $DataPeserta = array(
                'id' => $PesertaID, 'nama' => $_REQUEST['nama'], 'kelamin' => $_REQUEST['kelamin'],
                'telpon' => $_REQUEST['telpon'], 'jurusan' => $_REQUEST['jurusan'],
                'sekolah' => (isset($_REQUEST['sekolah'])) ? $_REQUEST['sekolah'] : $this->_another_sekolah($_REQUEST['another_sekolah'])
            );
            $DataHasil = array(
                'peserta_id' => $DataPeserta['id'], 'encrypted' => md5($DataPeserta['id']), 'q_number' => 0
            );
            if ($this->model()->insertTable('peserta', $DataPeserta) && $this->model()->insertTable('hasil', $DataHasil)) {
                if (isset($_REQUEST['sekolah'])) {
                    $DataSekolah = $this->model()->getWhere('sekolah', 'id', $DataPeserta['sekolah']);
                    $NamaSekolah = $DataSekolah['sekolah'];
                } else {
                    $NamaSekolah = strtoupper($_REQUEST['another_sekolah']);
                }
                $this->session('peserta')->set(array(
                    'id' => $DataPeserta['id'], 'nama' => $DataPeserta['nama'], 'number' => 0,
                    'sekolah' => $NamaSekolah
                ));
                Cookie::set(array('PARTIDAT' => $DataHasil['encrypted']), 7);
                Redirect('parti/success');
            } else {
                Redirect();
            }
        } else {
            Redirect();
        }
    }

    private function _another_sekolah($data)
    {
        $Result = null;
        $SekolahID = date('ymdHis');
        while ($this->model()->getWhere('sekolah_temp', 'id', $SekolahID)) {
            $SekolahID = strval($SekolahID + 1);
        }
        $DataSekolah = array('id' => $SekolahID, 'sekolah' => $data);
        if ($this->model()->insertTable('sekolah_temp', $DataSekolah)) $Result = $DataSekolah['id'];
        return $Result;
    }

    public function success()
    {
        if ($this->session('peserta')->check('id')) {
            $data['title'] = 'Pendaftaran Berhasil';
            $data['layout_type'] = 'navbar';
            $data['plugins'] = 'basic|fontawesome|icheck';
            $this->view('template/base.head', $data);
            $this->view('template/base.navbar', $data);
            $this->view('parti/success');
            $this->view('template/user.footer');
            $this->view('template/base.foot', $data);
        } else {
            Redirect();
        }
    }

    public function newparti()
    {
        Cookie::set(array('PARTIDAT' => '00000000'));
        $this->session('peserta')->remove();
        Redirect();
    }

    public function delete($id = null)
    {
        if (!$this->helper('Login')->access([1, 2]) || $id === null) {
            Redirect('dashboard');
        } else {
            $DataPeserta = $this->model()->getWhere('peserta', 'id', $id);
            if ($DataPeserta) {
                $DeletedRow = 0;
                if ($this->model()->deleteRecord('peserta', 'id', $id)) $DeletedRow++;
                if ($this->model()->deleteRecord('hasil', 'peserta_id', $id)) $DeletedRow++;
                if ($DeletedRow == 2) {
                    Flasher::setFlash('Data Peserta - ' . $DataPeserta['nama'], 'telah dihapus', 'info');
                }
            }
            Redirect('data/participant');
        }
    }

    public function edit($id = null)
    {
        if (!$this->helper('Login')->access([1, 2]) || $id === null) {
            Redirect('data/participant');
        } else {
            $Peserta = $this->model()->getWhere('peserta', 'id', $id);
            if ($Peserta) {
                $data['title'] = 'Ubah Data Peserta';
                $data['layout_type'] = 'sidebar';
                $data['plugins'] = 'basic|fontawesome|scrollbar';
                $data['peserta'] = $Peserta;
                $this->view('template/base.head', $data);
                $this->view('template/user.navbar', $data);
                $this->view('template/user.sidebar', $data);
                $this->view('parti/edit', $data);
                $this->view('template/user.footer');
                $this->view('template/base.foot', $data);
            } else {
                Redirect('data/participant');
            }
        }
    }
}
