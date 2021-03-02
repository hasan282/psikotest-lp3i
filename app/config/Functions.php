<?php

function BaseURL($url = '')
{
    $baseSSL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $baseHOST = $_SERVER['HTTP_HOST'];
    $customURL = ($url != '') ? '/' . $url : '';
    $addFolder = (ADDED_FOLDER != null || ADDED_FOLDER != '') ? '/' . ADDED_FOLDER : '';
    if (WEB_HOSTING) {
        return $baseSSL . '://' . $baseHOST . $addFolder . $customURL;
    } else {
        return $baseSSL . '://' . $baseHOST . '/' . HTDOCS_FOLDER . $addFolder . $customURL;
    }
}

function Redirect($url = '', $baseURL = true)
{
    $directTo = ($baseURL) ? BaseURL($url) : $url;
    header('location: ' . $directTo);
}

function CheckURL($url, $autoRedirect = false)
{
    $urlSplit = explode('://', $url);
    if (WEB_HOSTING && $urlSplit[0] != 'https') {
        $getHeader = @get_headers('https://' . $urlSplit[1]);
        if (!$getHeader || $getHeader[0] == 'HTTP/1.0 404 Not Found') {
            return false;
        } else {
            if ($autoRedirect) Redirect('https://' . $urlSplit[1], false);
            return true;
        }
    }
    return true;
}
