<?php

class export extends Controller
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
            $data['title'] = 'Ekspor Data';
            $data['layout_type'] = 'sidebar';
            $data['plugins'] = 'basic|fontawesome|scrollbar|icheck';
            $data['jscript'] = 'export.funct';
            $this->view('template/base.head', $data);
            $this->view('template/user.navbar', $data);
            $this->view('template/user.sidebar', $data);
            $this->view('export/main');
            $this->view('template/user.footer');
            $this->view('template/base.foot', $data);
        } else {
            Redirect('user/login');
        }
    }

    public function preview()
    {
        if (isset($_POST['action'])) {
            $Filters = array();
            $MaxQuestion = $this->model()->recordCount('SELECT SUBSTRING(id,1,3) AS ids FROM question GROUP BY ids');
            $ColName = array(
                'nama' => 'NAMA', 'kelamin' => 'JENIS KELAMIN', 'telpon' => 'NO. TELPON', 'sekolah' => 'SEKOLAH',
                'jurusan' => 'JURUSAN', 'tanggal' => 'TANGGAL', 'status' => 'STATUS TES', 'karakter' => 'KARAKTER'
            );
            foreach (array_keys($ColName) as $key) {
                if (!array_key_exists('check_' . $key, $_REQUEST) || $_REQUEST['check_' . $key] != 'on') unset($ColName[$key]);
            }
            if (isset($_REQUEST['kelamin']) && $_REQUEST['kelamin'] != 'Semua') $Filters['kelamin'] = $_REQUEST['kelamin'];
            if (isset($_REQUEST['sekolah']) && $_REQUEST['sekolah'] != 'Semua') $Filters['sekolah'] = $_REQUEST['sekolah'];
            if (isset($_REQUEST['status']) && $_REQUEST['status'] != 'Semua') {
                $QuerySign = array('< ', '= ');
                $Filters['proses'] = $QuerySign[$_REQUEST['status']] . $MaxQuestion;
            }
            $DataPeserta = $this->model('PesertaModel')->getExportData(array_keys($ColName), $_REQUEST['rows'], $Filters);
            $ExportConfig = array(
                'filename' => ($_REQUEST['filename'] == '') ? 'Data Peserta Psikotest' : $_REQUEST['filename'],
                'subject' => ($_REQUEST['sekolah'] == 'Semua') ? 'Data Peserta Psikotest' : 'Data Peserta Per-Sekolah'
            );
            for ($dp = 0; $dp < sizeof($DataPeserta); $dp++) {
                if (isset($ColName['telpon']) && $_REQUEST['phones'] == '62') $DataPeserta[$dp]['telpon'] = " +62" . ltrim($DataPeserta[$dp]['telpon'], 0);
                if (isset($ColName['tanggal'])) $DataPeserta[$dp]['id'] = $this->helper('Date')->toDate($DataPeserta[$dp]['id'], 12);
                if (isset($ColName['status'])) $DataPeserta[$dp]['q_number'] = ($DataPeserta[$dp]['q_number'] == $MaxQuestion) ? 'Selesai' : 'Belum Selesai (' . $DataPeserta[$dp]['q_number'] . '/' . $MaxQuestion . ')';
            }
            if ($_POST['action'] == 'preview') {
                $data['config'] = $ExportConfig;
                $data['peserta'] = $DataPeserta;
                $data['column'] = array_values($ColName);
                $this->view('export/view', $data);
            } elseif ($_POST['action'] == 'export') {
                $this->helper('ExportExcel')->export(array_values($ColName), $DataPeserta, $ExportConfig);
            } else {
                Redirect('export');
            }
        } else {
            Redirect('export');
        }
    }
}
