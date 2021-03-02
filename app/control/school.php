<?php

class school extends Controller
{
    public function index()
    {
        if ($this->helper('Login')->access([1, 2])) {
            $data['title'] = 'Data Sekolah';
            $data['layout_type'] = 'sidebar';
            $data['plugins'] = 'basic|fontawesome|scrollbar|icheck|sweetalert';
            $data['jscript'] = 'sekolah.funct';
            $this->view('template/base.head', $data);
            $this->view('template/user.navbar', $data);
            $this->view('template/user.sidebar', $data);
            $this->view('data/sekolah');
            $this->view('template/user.footer');
            $this->view('template/base.foot', $data);
        } else {
            Redirect('dashboard');
        }
    }

    public function add()
    {
        if ($this->helper('Login')->access([1, 2]) && isset($_POST['sekolah'])) {
            $data = array('id' => date('ymdHis'), 'sekolah' => $_REQUEST['sekolah'], 'kecamatan_id' => $_REQUEST['kecamatan']);
            if ($this->model()->insertTable('sekolah', $data)) {
                Flasher::setFlash($data['sekolah'], 'telah ditambahkan', 'info');
                Redirect('school');
            } else {
                Flasher::setFlash($data['sekolah'], 'tidak dapat ditambahkan', 'danger');
                Redirect('school');
            }
        } else {
            Redirect('school');
        }
    }

    public function delete($id = null)
    {
        if ($id === null || !$this->helper('Login')->access([1, 2])) {
            Redirect('school');
        } else {
            $Jumlah = $this->model()->recordCount("SELECT id FROM peserta WHERE sekolah = '" . $id . "'");
            $Sekolah = $this->model()->getWhere('sekolah', 'id', $id);
            if ($Jumlah === 0 && $Sekolah) {
                if ($this->model()->deleteRecord('sekolah', 'id', $id)) {
                    Flasher::setFlash($Sekolah['sekolah'], 'telah dihapus', 'info');
                    Redirect('school');
                } else {
                    Flasher::setFlash($Sekolah['sekolah'], 'tidak dapat dihapus', 'danger');
                    Redirect('school');
                }
            } else {
                Redirect('school');
            }
        }
    }

    public function merge()
    {
        if ($this->helper('Login')->access([1, 2]) && isset($_POST['merge_sekolah'])) {
            $TempSekolah = explode('@', $_REQUEST['merge_sekolah']);
            $MergeTo = $_REQUEST['com_sekolah'];
            $ResultMerge = 0;
            foreach ($TempSekolah as $ts) {
                $ResultMerge += $this->model()->recordCount("UPDATE peserta SET sekolah = '" . $MergeTo . "' WHERE sekolah = '" . $ts . "'");
                if ($this->model()->deleteRecord('sekolah_temp', 'id', $ts)) $ResultMerge++;
            }
            Redirect('school');
        } else {
            Redirect('school');
        }
    }

    public function detail($id = null)
    {
        if ($id === null) {
            Redirect('school');
        } else {
            $DataSekolah = $this->model()->getWhere('sekolah', 'id', $id);
            if ($DataSekolah) {
                $data['title'] = 'Data Sekolah';
                $data['layout_type'] = 'sidebar';
                $data['plugins'] = 'basic|fontawesome|scrollbar';
                $data['sekolah'] = $DataSekolah;
                $this->view('template/base.head', $data);
                $this->view('template/user.navbar', $data);
                $this->view('template/user.sidebar', $data);
                $this->view('data/sekolah.detail', $data);
                $this->view('template/user.footer');
                $this->view('template/base.foot', $data);
            } else {
                Redirect('school');
            }
        }
    }
}
