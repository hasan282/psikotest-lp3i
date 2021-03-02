<?php

class App
{
    protected $controller = 'home';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseURL();
        if (file_exists('app/control/' . $url[0] . '.php')) {
            $this->controller = $url[0];
            unset($url[0]);
        }
        require_once 'app/control/' . $this->controller . '.php';
        $this->controller = new $this->controller;
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        if (!empty($url)) {
            $this->params = array_values($url);
        }
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL()
    {
        $url = null;
        switch (HTACCESS_TYPE) {
            case 'CI':
                $url = $_SERVER['REQUEST_URI'];
                if ($_SERVER['QUERY_STRING'] != '' && $_SERVER['QUERY_STRING'] != null) $url = rtrim($url, $_SERVER['QUERY_STRING']);
                $url = trim(rtrim($url, '?'), '/');
                if (!WEB_HOSTING) $url = ltrim(ltrim($url, HTDOCS_FOLDER), '/');
                if (ADDED_FOLDER != null && ADDED_FOLDER != '') $url = ltrim(ltrim($url, ADDED_FOLDER), '/');
                $url = filter_var($url, FILTER_SANITIZE_URL);
                $url = explode('/', $url);
                break;
            case 'GET':
                if (isset($_GET['url'])) $url = ltrim(rtrim($_GET['url'], '/'), '/');
                $url = filter_var($url, FILTER_SANITIZE_URL);
                $url = explode('/', $url);
                break;
            default:
                break;
        }
        return $url;
    }
}
