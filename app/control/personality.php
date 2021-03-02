<?php

class personality extends Controller
{
    public function __construct()
    {
        if (!$this->session('userdata')->check('username')) {
            $this->helper('Login')->cookie_login();
        }
    }

    public function index()
    {
        $data['title'] = 'Tipe Karakter';
        $data['layout_type'] = 'navbar';
        $data['plugins'] = 'basic|fontawesome';
        $this->view('template/base.head', $data);
        $this->view('template/base.navbar', $data);
        $this->view('person/main');
        $this->view('template/user.footer');
        $this->view('template/base.foot', $data);
    }
}
