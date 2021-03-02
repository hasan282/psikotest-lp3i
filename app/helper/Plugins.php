<?php

class Plugins
{
    private function _plugin($name = null)
    {
        $Plugin = array(
            //-- ['type' => 'css/js', 'position' => 'top/bottom', 'link' => '']
            'basic' => array(
                ['type' => 'css', 'position' => 'top', 'link' => 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700'],
                ['type' => 'css', 'position' => 'top', 'link' => BaseURL('vendor/dist/css/adminlte.min.css')],
                ['type' => 'css', 'position' => 'top', 'link' => BaseURL('asset/css/basic.custom.css?s=') . mt_rand(100, 999)],
                ['type' => 'js', 'position' => 'top', 'link' => BaseURL('vendor/plugins/jquery/jquery.min.js')],
                ['type' => 'js', 'position' => 'bottom', 'link' => BaseURL('vendor/plugins/bootstrap/js/bootstrap.bundle.min.js')],
                ['type' => 'js', 'position' => 'bottom', 'link' => BaseURL('vendor/dist/js/adminlte.min.js')]
            ),
            'fontawesome' => array(
                ['type' => 'css', 'position' => 'top', 'link' => BaseURL('vendor/plugins/fontawesome-free/css/all.min.css')]
            ),
            'ionicon' => array(
                ['type' => 'css', 'position' => 'top', 'link' => 'https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css']
            ),
            'icheck' => array(
                ['type' => 'css', 'position' => 'top', 'link' => BaseURL('vendor/plugins/icheck-bootstrap/icheck-bootstrap.min.css')]
            ),
            'scrollbar' => array(
                ['type' => 'css', 'position' => 'top', 'link' => BaseURL('vendor/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')],
                ['type' => 'js', 'position' => 'bottom', 'link' => BaseURL('vendor/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')]
            ),
            'sweetalert' => array(
                ['type' => 'js', 'position' => 'top', 'link' => 'https://cdn.jsdelivr.net/npm/sweetalert2@10']
            ),
            'select' => array(
                ['type' => 'css', 'position' => 'top', 'link' => BaseURL('vendor/plugins/select2/css/select2.min.css')],
                ['type' => 'css', 'position' => 'top', 'link' => BaseURL('vendor/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')],
                ['type' => 'js', 'position' => 'bottom', 'link' => BaseURL('vendor/plugins/select2/js/select2.full.min.js')]
            ),
            'inputmask' => array(
                ['type' => 'js', 'position' => 'bottom', 'link' => BaseURL('vendor/plugins/moment/moment.min.js')],
                ['type' => 'js', 'position' => 'bottom', 'link' => BaseURL('vendor/plugins/inputmask/min/jquery.inputmask.bundle.min.js')]
            ),
            'datepicker' => array(
                ['type' => 'css', 'position' => 'top', 'link' => BaseURL('vendor/plugins/daterangepicker/daterangepicker.css')],
                ['type' => 'js', 'position' => 'bottom', 'link' => BaseURL('vendor/plugins/daterangepicker/daterangepicker.js')]
            ),
            'datatable' => array(
                ['type' => 'css', 'position' => 'top', 'link' => BaseURL('vendor/plugins/datatables-bs4/css/dataTables.bootstrap4.css')],
                ['type' => 'js', 'position' => 'bottom', 'link' => BaseURL('vendor/plugins/datatables/jquery.dataTables.js')],
                ['type' => 'js', 'position' => 'bottom', 'link' => BaseURL('vendor/plugins/datatables-bs4/js/dataTables.bootstrap4.js')]
            )
        );
        return ($name != null && array_key_exists($name, $Plugin)) ? $Plugin[$name] : false;
    }

    public function Top($plugin = 'none')
    {
        $cssPlugin = '';
        $jsPlugin = '';
        $PluginName = explode('|', $plugin);
        foreach ($PluginName as $pn) {
            if ($this->_plugin($pn)) foreach ($this->_plugin($pn) as $p) {
                if ($p['type'] == 'css' && $p['position'] == 'top') $cssPlugin .= '<link rel="stylesheet" href="' . $p['link'] . '">';
                if ($p['type'] == 'js' && $p['position'] == 'top') $jsPlugin .= '<script src="' . $p['link'] . '"></script>';
            }
        }
        return $cssPlugin . $jsPlugin;
    }

    public function Bottom($plugin = 'none')
    {
        $jsPlugin = '';
        $PluginName = explode('|', $plugin);
        foreach ($PluginName as $pn) {
            if ($this->_plugin($pn)) foreach ($this->_plugin($pn) as $p) {
                if ($p['type'] == 'js' && $p['position'] == 'bottom') $jsPlugin .= '<script src="' . $p['link'] . '"></script>';
            }
        }
        return $jsPlugin;
    }
}
