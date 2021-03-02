<?php

class Session
{
    private $session_name;
    private $result;

    public function __construct($name = null)
    {
        $this->session_name = $name;
    }

    public function get($key = null)
    {
        if ($this->session_name === null) {
            $this->result = $_SESSION;
        } else {
            $ses_key = $this->session_name;
            if ($key === null) {
                $this->result = (isset($_SESSION[$ses_key])) ? $_SESSION[$ses_key] : false;
            } else {
                $this->result = (isset($_SESSION[$ses_key][$key])) ? $_SESSION[$ses_key][$key] : false;
            }
        }
        return $this->result;
    }

    public function set($data = [])
    {
        if ($this->session_name === null) {
            $this->result = false;
        } else {
            $_SESSION[$this->session_name] = $data;
            $this->result = true;
        }
        return $this->result;
    }

    public function remove($key = null)
    {
        if ($this->session_name === null || !isset($_SESSION[$this->session_name])) {
            $this->result = false;
        } else {
            if ($key === null) {
                unset($_SESSION[$this->session_name]);
                $this->result = true;
            } else {
                if (isset($_SESSION[$this->session_name][$key])) {
                    unset($_SESSION[$this->session_name][$key]);
                    $this->result = true;
                } else {
                    $this->result = false;
                }
            }
        }
        return $this->result;
    }

    public function check($key = null)
    {
        $Result = false;
        if ($this->session_name != null && $key != null) {
            $Result = isset($_SESSION[$this->session_name][$key]);
        } elseif ($this->session_name != null && $key === null) {
            $Result = isset($_SESSION[$this->session_name]);
        }
        return $Result;
    }
}
