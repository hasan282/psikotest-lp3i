<?php

class Login extends Controller
{
    public function cookie_login()
    {
        $USRENCYRP = (isset($_COOKIE['PSENCYRP'])) ? $_COOKIE['PSENCYRP'] : '000000';
        $USRLOGDAT = (isset($_COOKIE['PSLOGDAT'])) ? $_COOKIE['PSLOGDAT'] : '000000';
        if ($USRENCYRP != '000000' && $USRLOGDAT != '000000') {
            $User = $this->model()->getThisQuery('SELECT username, password FROM user');
            $TrueUser = null;
            foreach ($User as $us) {
                $UserName = md5($us['username']);
                $UserLog = md5($UserName . $us['password']);
                if ($UserName === $USRENCYRP && $UserLog === $USRLOGDAT) $TrueUser = $us['username'];
            }
            if ($TrueUser != null) $this->_login($TrueUser);
        }
    }

    private function _login($username)
    {
        $User = $this->model()->getWhere('user', 'username', $username);
        $Access = $this->model()->getWhere('access', 'id', $User['access']);
        $UserData = array(
            'username' => $User['username'], 'name' => $User['name'],
            'access' => $User['access'], 'role' => $Access['access'],
            'photo' => $User['photo'], 'login' => $User['login'],
            'created' => $User['id']
        );
        if (date('Y-m-d') != $User['last_login']) {
            $NewLogin = array('login' => ($User['login'] + 1), 'last_login' => date('Y-m-d'));
            if ($this->model()->updateRecord('user', $NewLogin, array('id' => $User['id']))) $UserData['login'] = $NewLogin['login'];
        }
        $this->session('userdata')->set($UserData);
    }

    public function access($code = null)
    {
        $AccessCode = $this->session('userdata')->get('access');
        if ($code === null || !$AccessCode) {
            return false;
        } else {
            $Result = false;
            if (is_array($code)) {
                if (sizeof($code) > 0 && in_array($AccessCode, $code)) $Result = true;
            } else {
                if ($AccessCode == $code) $Result = true;
            }
            return $Result;
        }
    }
}
