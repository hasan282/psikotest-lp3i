<?php

class dashboard extends Controller
{
    public function __construct()
    {
        if (!$this->session('userdata')->check('username')) {
            $this->helper('Login')->cookie_login();
        }
    }

    public function index()
    {
        if ($this->session('userdata')->check('username')) {
            $data['title'] = 'Dashboard';
            $data['layout_type'] = 'sidebar';
            $data['plugins'] = 'basic|fontawesome|scrollbar';
            $this->view('template/base.head', $data);
            $this->view('template/user.navbar', $data);
            $this->view('template/user.sidebar', $data);
            $this->view('dashboard/dash' . $this->session('userdata')->get('access'));
            $this->view('template/user.footer');
            $this->view('template/base.foot', $data);
        } else {
            Redirect('user/login');
        }
    }
}
