<?php

class home extends Controller
{
    public function __construct()
    {
        if (!$this->session('peserta')->check('id')) {
            $this->helper('Participant')->setData();
        }
        if (!$this->session('userdata')->check('username')) {
            $this->helper('Login')->cookie_login();
        }
    }

    public function index()
    {
        $data['title'] = 'Halaman Utama';
        $data['layout_type'] = 'navbar';
        $data['plugins'] = 'basic|fontawesome|select|icheck';
        $data['jscript'] = 'home.funct';
        $this->view('template/base.head', $data);
        $this->view('template/base.navbar', $data);
        $this->view('home/main');
        $this->view('template/user.footer');
        $this->view('template/base.foot', $data);
    }

    public function index2()
    {
        $this->view('home/index2');
    }

    public function croptry()
    {
        $data['title'] = 'Crop Trial';
        $data['layout_type'] = 'navbar';
        $data['plugins'] = 'basic|fontawesome|finecrop';
        $data['jscript'] = 'croptry';
        $this->view('template/base.head', $data);
        $this->view('home/crop');
        $this->view('template/base.foot', $data);
    }
}
