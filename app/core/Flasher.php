<?php

class Flasher
{
    public static function setFlash($pesan, $aksi, $tipe)
    {
        $_SESSION['flash'] = [
            'pesan' => $pesan,
            'aksi'  => $aksi,
            'tipe'  => $tipe
        ];
    }

    public static function flash()
    {
        if (isset($_SESSION['flash'])) {
            $psn = $_SESSION['flash']['pesan'];
            $act = $_SESSION['flash']['aksi'];
            $typ = $_SESSION['flash']['tipe'];
            $flashMessage = "<div class='alert alert-$typ alert-dismissible fade show'>" .
                "$psn <strong>$act</strong><button type='button' class='close' data-dismiss='alert' aria-label='Close'>" .
                "<span aria-hidden='true'>&times;</span></button></div>";
            echo $flashMessage;
            unset($_SESSION['flash']);
        }
    }

    public static function adminlte_flash()
    {
        $icons = array('danger' => 'fas fa-ban', 'warning' => 'fas fa-exclamation-triangle', 'info' => 'fas fa-info', 'success' => 'fas fa-check');
        if (isset($_SESSION['flash'])) {
            $pesan = $_SESSION['flash']['pesan'];
            $aksi = $_SESSION['flash']['aksi'];
            $tipe = $_SESSION['flash']['tipe'];
            $icon = (array_key_exists($tipe, $icons)) ? $icons[$tipe] : '';
            $flashMessage = '<div class="alert alert-' . $tipe . ' alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h5><i class="icon ' . $icon . '"></i> ' . $aksi . '</h5><p>' . $pesan . '</p></div>';
            echo $flashMessage;
            unset($_SESSION['flash']);
        }
    }
}
