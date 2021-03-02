<?php

class Controller
{
    protected function view($view, $data = [])
    {
        $ArrayKeys = array_keys($data);
        foreach ($ArrayKeys as $ark) {
            ${$ark} = $data[$ark];
        }
        require_once 'app/view/' . $view . '.php';
    }

    protected function model($model = 'BasicModel')
    {
        require_once 'app/model/' . $model . '.php';
        return new $model;
    }

    protected function helper($helper, $param = '')
    {
        require_once 'app/helper/' . $helper . '.php';
        return new $helper($param);
    }

    protected function session($name = null)
    {
        require_once 'app/helper/Session.php';
        return new Session($name);
    }
}
