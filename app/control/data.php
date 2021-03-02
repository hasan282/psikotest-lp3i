<?php

class data extends Controller
{
    public function __construct()
    {
        if (!$this->session('userdata')->check('username')) {
            $this->helper('Login')->cookie_login();
        }
    }

    public function index()
    {
        Redirect('dashboard');
    }

    public function participant()
    {
        if ($this->helper('Login')->access([1, 2])) {
            $data['title'] = 'Data Peserta';
            $data['layout_type'] = 'sidebar';
            $data['plugins'] = 'basic|fontawesome|scrollbar|sweetalert|select';
            $data['jscript'] = 'peserta.funct';
            $this->view('template/base.head', $data);
            $this->view('template/user.navbar', $data);
            $this->view('template/user.sidebar', $data);
            $this->view('data/peserta');
            $this->view('template/user.footer');
            $this->view('template/base.foot', $data);
        } else {
            Redirect('dashboard');
        }
    }

    public function user()
    {
        if ($this->helper('Login')->access(1)) {
            $data['title'] = 'Data Pengguna';
            $data['layout_type'] = 'sidebar';
            $data['plugins'] = 'basic|fontawesome|scrollbar';
            $this->view('template/base.head', $data);
            $this->view('template/user.navbar', $data);
            $this->view('template/user.sidebar', $data);
            $this->view('data/user');
            $this->view('template/user.footer');
            $this->view('template/base.foot', $data);
        } else {
            Redirect('dashboard');
        }
    }

    public function address()
    {
        $Area = (isset($_REQUEST['area'])) ? $_REQUEST['area'] : null;
        $Key = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : null;
        switch ($Area) {
            case 'provinsi':
                $this->helper('ApiGet')->provinsi();
                break;
            case 'kabupaten':
                if ($Key === null) {
                    $this->helper('ApiGet')->false_return();
                } else {
                    $this->helper('ApiGet')->sub_area('kabupaten', $Key);
                }
                break;
            case 'kecamatan':
                if ($Key === null) {
                    $this->helper('ApiGet')->false_return();
                } else {
                    $this->helper('ApiGet')->sub_area('kecamatan', $Key);
                }
                break;
            default:
                $this->helper('ApiGet')->false_return();
                break;
        }
    }
}
