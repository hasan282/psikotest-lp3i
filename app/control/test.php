<?php

class test extends Controller
{
    public function index()
    {
        if ($this->session('peserta')->check('id')) {
            $data['title'] = 'Tes Kepribadian';
            $data['layout_type'] = 'navbar';
            $data['plugins'] = 'basic|fontawesome';
            $data['jscript'] = 'test.funct';
            $this->view('template/base.head', $data);
            $this->view('template/base.navbar', $data);
            $this->view('test/main');
            $this->view('template/user.footer');
            $this->view('template/base.foot', $data);
        } else {
            Redirect();
        }
    }

    public function result()
    {
        $MaxQuestion = $this->model()->recordCount('SELECT SUBSTRING(id,1,3) AS ids FROM question GROUP BY ids');
        if ($this->session('peserta')->get('number') == $MaxQuestion) {
            $data['title'] = 'Hasil Akhir';
            $data['layout_type'] = 'navbar';
            $data['plugins'] = 'basic|fontawesome';
            $this->view('template/base.head', $data);
            $this->view('template/base.navbar', $data);
            $this->view('test/result');
            $this->view('template/user.footer');
            $this->view('template/base.foot', $data);
        } else {
            Redirect('test');
        }
    }

    public function continuetest($enc = null)
    {
        if ($enc === null) {
            Redirect('test');
        } else {
            $Peserta = $this->model('SearchModel')->srcContinue($enc);
            if ($Peserta && $Peserta['encrypted'] === $enc) {
                $data['title'] = 'Konfirmasi Peserta';
                $data['layout_type'] = 'login';
                $data['plugins'] = 'basic|fontawesome';
                $data['peserta'] = $Peserta;
                $this->view('template/base.head', $data);
                $this->view('test/continue', $data);
                $this->view('template/base.foot', $data);
            } else {
                Redirect('test');
            }
        }
    }

    public function confirm($enc = null)
    {
        if ($enc === null || !isset($_REQUEST['telp'])) {
            print json_encode(array('result' => false));
        } else {
            $Result = false;
            $Peserta = $this->model('SearchModel')->srcContinue($enc);
            if ($Peserta['telpon'] == $_REQUEST['telp']) {
                $Result = true;
                Cookie::set(array('PARTIDAT' => $enc), 3);
                $this->helper('Participant')->setData($enc);
            }
            print json_encode(array('result' => $Result));
        }
    }
}
