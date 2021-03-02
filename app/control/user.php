<?php

class user extends Controller
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
            $data['title'] = 'Pengguna';
            $data['layout_type'] = 'sidebar';
            $data['plugins'] = 'basic|fontawesome|scrollbar';
            $this->view('template/base.head', $data);
            $this->view('template/user.navbar', $data);
            $this->view('template/user.sidebar', $data);
            $this->view('user/main');
            $this->view('template/user.footer');
            $this->view('template/base.foot', $data);
        } else {
            Redirect('user/login');
        }
    }

    public function login()
    {
        if ($this->session('userdata')->check('username')) {
            Redirect('dashboard');
        } else {
            $data['title'] = 'Masuk Pengguna';
            $data['layout_type'] = 'login';
            $data['plugins'] = 'basic|fontawesome|icheck';
            $this->view('template/base.head', $data);
            $this->view('user/login');
            $this->view('template/base.foot', $data);
        }
    }

    public function authorize()
    {
        if ($_POST && isset($_POST['username'])) {
            $User = $this->model()->getWhere('user', 'username', $_POST['username']);
            if ($User) {
                if ($User['password'] === md5($_POST['password']) && $User['is_active'] == '1') {
                    $Access = $this->model()->getWhere('access', 'id', $User['access']);
                    $UserData = array(
                        'created' => $User['id'], 'username' => $User['username'], 'name' => $User['name'],
                        'access' => $User['access'], 'role' => $Access['access'],
                        'photo' => $User['photo'], 'login' => $this->_login_rec($User['login'], $User['last_login'], $User['id'])
                    );
                    $DataCookie = array('PSENCYRP' => md5($User['username']), 'PSLOGDAT' => md5(md5($User['username']) . $User['password']));
                    $this->session('userdata')->set($UserData);
                    Cookie::set($DataCookie, 3);
                    Redirect('dashboard');
                } else {
                    if ($User['password'] === md5($_POST['password'])) {
                        Flasher::setFlash('Nama pengguna mungkin tidak aktif atau di nonaktif', 'Tidak Aktif', 'warning');
                        Redirect('user/login');
                    } else {
                        Flasher::setFlash('Kata sandi anda tidak cocok', 'Password Salah', 'danger');
                        Redirect('user/login');
                    }
                }
            } else {
                Flasher::setFlash('Nama pengguna anda tidak terdaftar', 'Tidak Terdaftar', 'danger');
                Redirect('user/login');
            }
        } else {
            Redirect('user/login');
        }
    }

    private function _login_rec($login, $last_login, $id)
    {
        $LoginTotal = $login;
        if (date('Y-m-d') != $last_login) {
            $NewLogin = array('login' => ($login + 1), 'last_login' => date('Y-m-d'));
            if ($this->model()->updateRecord('user', $NewLogin, array('id' => $id))) $LoginTotal = $NewLogin['login'];
        }
        return $LoginTotal;
    }

    public function logout()
    {
        $this->session('userdata')->remove();
        $DataCookie = array('PSENCYRP' => '000000', 'PSLOGDAT' => '000000');
        Cookie::set($DataCookie);
        Redirect();
    }

    public function setting()
    {
        if ($this->session('userdata')->check('username')) {
            $data['title'] = 'Pengaturan Akun';
            $data['layout_type'] = 'sidebar';
            $data['plugins'] = 'basic|fontawesome|scrollbar';
            $this->view('template/base.head', $data);
            $this->view('template/user.navbar', $data);
            $this->view('template/user.sidebar', $data);
            $this->view('user/setting');
            $this->view('template/user.footer');
            $this->view('template/base.foot', $data);
        } else {
            Redirect('user/login');
        }
    }
}
