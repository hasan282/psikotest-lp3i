<?php

class ApiGet
{
    private $api_url = 'https://sta.smallsite.club/api/';
    private $data, $result;

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
    }

    public function provinsi()
    {
        $this->data = @file_get_contents($this->api_url . 'provinsi');
        if (!$this->data) {
            $this->result = array('status' => false);
        } else {
            $Proinsi = (array)json_decode($this->data)->data;
            $this->result = array('status' => true, 'data' => $Proinsi);
        }
        print json_encode($this->result);
    }

    public function sub_area($locate, $id)
    {
        $this->data = @file_get_contents($this->api_url . 'lokasi/' . $locate . '/' . $id);
        if (!$this->data) {
            $this->result = array('status' => false);
        } else {
            $Location = (array)json_decode($this->data);
            if ($Location['status']) {
                $ResultData = (array)$Location['data'];
                $this->result = array('status' => true, 'data' => $ResultData);
            } else {
                $this->result = array('status' => false);
            }
        }
        print json_encode($this->result);
    }

    public function false_return()
    {
        $this->result = array('status' => false);
        print json_encode($this->result);
    }
}
