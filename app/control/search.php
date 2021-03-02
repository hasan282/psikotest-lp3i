<?php

class search extends Controller
{
    public function __construct()
    {
        if (!$this->session('userdata')->check('username')) {
            $this->helper('Login')->cookie_login();
        }
    }

    public function index()
    {
        $data['title'] = 'Pencarian';
        $data['layout_type'] = 'navbar';
        $data['plugins'] = 'basic|fontawesome|select|inputmask|datepicker';
        $data['jscript'] = 'search.funct';
        $this->view('template/base.head', $data);
        $this->view('template/base.navbar', $data);
        $this->view('search/main');
        $this->view('template/user.footer');
        $this->view('template/base.foot', $data);
    }
}
