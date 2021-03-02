<?php

class api extends Controller
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
    }

    public function index()
    {
        $this->_BAD_REQUEST();
    }

    public function question($number = null)
    {
        if ($number === null) {
            $this->_BAD_REQUEST();
        } else {
            $ReqMethod = $_SERVER['REQUEST_METHOD'];
            $number = (ctype_digit($number)) ? intval($number) : 0;
            $number = ($number < 10) ? 'Q0' . $number . '%' : 'Q' . $number . '%';
            switch ($ReqMethod) {
                case 'GET':
                    $Question = $this->model()->getWhere('question', 'id', $number, true);
                    $Result = ($Question) ? array('status' => true, 'data' => $Question) : array('status' => false);
                    print json_encode($Result);
                    break;
                case 'POST':
                    $PostValue = true;
                    $id = $this->model()->getThisQuery("SELECT id FROM question WHERE id LIKE '" . $number . "'");
                    if ($id) {
                        foreach ($id as $i) if (!isset($_REQUEST[($i['id'])])) $PostValue = false;
                    } else {
                        $PostValue = false;
                    }
                    if ($PostValue) {
                        $Result = true;
                        foreach ($id as $i) if (!$this->model()->updateRecord('question', ['question' => $_REQUEST[($i['id'])]], ['id' => $i['id']])) $Result = false;
                    } else {
                        $Result = false;
                    }
                    print json_encode(array('status' => $Result));
                    break;
                default:
                    $this->_BAD_REQUEST();
                    break;
            }
        }
    }

    public function answer($id = null)
    {
        if ($id === null) {
            $this->_BAD_REQUEST();
        } else {
            $ReqMethod = $_SERVER['REQUEST_METHOD'];
            switch ($ReqMethod) {
                case 'GET':
                    print json_encode(array('result' => null));
                    break;
                case 'POST':
                    $AnswerData = $this->model()->getWhere('hasil', 'peserta_id', $id);
                    $Result = ($AnswerData) ? $this->ans_POST($AnswerData, $_REQUEST) : array('status' => false);
                    print json_encode($Result);
                    break;
                default:
                    $this->_BAD_REQUEST();
                    break;
            }
        }
    }

    private function ans_POST($data, $req)
    {
        $Result = array('status' => false);
        if (isset($req['number']) && isset($req['answer'])) {
            if ($req['number'] - 1 == $data['q_number']) {
                $Answer = ($data['answers'] == null) ? [] : explode(',', $data['answers']);
                $Answer[($req['number'] - 1)] = $req['answer'];
                $EditData = array(
                    'q_number' => $req['number'],
                    'answers' => implode(',', $Answer)
                );
                if ($this->model()->updateRecord('hasil', $EditData, array('peserta_id' => $data['peserta_id']))) {
                    $Result = array('status' => true, 'number' => ($req['number'] + 1));
                    $_SESSION['peserta']['number'] = $data['q_number'] + 1;
                }
            }
        }
        return $Result;
    }

    public function search()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            $this->_BAD_REQUEST();
        } else {
            $Result = array('status' => false);
            if (isset($_REQUEST['nm']) && $_REQUEST['nm'] != '') {
                $DataSearch = $this->model('SearchModel')->result($_REQUEST);
                if ($DataSearch) $Result = array('status' => true, 'data' => $DataSearch);
            }
            print json_encode($Result);
        }
    }

    public function peserta()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            $this->_BAD_REQUEST();
        } else {
            $idPeserta = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : null;
            $PageNumber = (isset($_REQUEST['page'])) ? (int)$_REQUEST['page'] : 1;
            $ViewList = (isset($_REQUEST['list'])) ? (int)$_REQUEST['list'] : 10;
            $Filter = array();
            if (isset($_REQUEST['nama']) && $_REQUEST['nama'] != '') $Filter['nama'] = $_REQUEST['nama'];
            if (isset($_REQUEST['sekolah']) && $_REQUEST['sekolah'] != '') $Filter['sekolah'] = $_REQUEST['sekolah'];
            if (isset($_REQUEST['proses'])) {
                if ($_REQUEST['proses'] == '0') $Filter['proses'] = false;
                if ($_REQUEST['proses'] == '1') $Filter['proses'] = true;
            }
            $DataPeserta = $this->helper('Participant')->getData($idPeserta, $PageNumber, $ViewList, $Filter);
            $Result = ($DataPeserta) ? array('status' => true, 'data' => $DataPeserta['data'], 'total' => $DataPeserta['total']) : array('status' => false);
            print json_encode($Result);
        }
    }

    private function _BAD_REQUEST()
    {
        http_response_code(400);
        print json_encode(array('status' => false));
    }
}
