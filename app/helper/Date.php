<?php

class Date
{
    private $bulan = 'Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember';
    private $mnt = 'Jan|Feb|Mar|Apr|Mei|Jun|Jul|Ags|Sep|Okt|Nov|Des';

    public function convert($date, $type)
    {
        switch ($type) {
            case 10: // 2020-02-28 --> 28-02-2020
                $dt = explode('-', $date);
                $result = $dt[2] . '-' . $dt[1] . '-' . $dt[0];
                break;
            case 15: // 28/02/2020 --> 2020-02-28
                $dt = explode('/', $date);
                $result = $dt[2] . '-' . $dt[1] . '-' . $dt[0];
                break;
            case 20: // 2020-02-28 --> 28 Februari 2020
                $dt = explode('-', $date);
                $bln = explode('|', $this->bulan);
                $result = $dt[2] . ' ' . $bln[$dt[1] - 1] . ' ' . $dt[0];
                break;
            case 21: // 2020-02-28 --> 28 Feb
                $dt = explode('-', $date);
                $bln = explode('|', $this->mnt);
                $result = $dt[2] . ' ' . $bln[$dt[1] - 1];
                break;
            default:
                $result = null;
                break;
        }
        return $result;
    }

    public function toDate($id, $type)
    {
        $year = substr($id, 0, 2);
        $month = substr($id, 2, 2);
        $day = substr($id, 4, 2);
        switch ($type) {
            case 10: // 2020-02-28
                $date = '20' . $year . '-' . $month . '-' . $day;
                break;
            case 11: // 28-02-2020
                $date = $day . '-' . $month . '-' . '20' . $year;
                break;
            case 12: // 28/02/2020
                $date = $day . '/' . $month . '/' . '20' . $year;
                break;
            case 20: // 28 Februari 2020
                $bln = explode('|', $this->bulan);
                $date = $day . ' ' . $bln[$month - 1] . ' ' . '20' . $year;
                break;
            case 21: // 28-Feb-2020
                $bln = explode('|', $this->mnt);
                $date = $day . '-' . $bln[$month - 1] . '-' . $year;
                break;
            default:
                $date = null;
                break;
        }
        return $date;
    }
}
