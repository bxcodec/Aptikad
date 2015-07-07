<?php

namespace backend\modules\aitk\models;

use yii\base\Model;
use Yii;

/*
 *     Name :  Iman Syahputra Situmorang
 *     NIM  :  11113064
 *     Date :  14/May/2015

 */

/**
 * Description of FormIzin
 *
 * @author Takiya
 */
class FormIzin extends Model {

    public $sesiList;
    public $nim;
    public $kelas;
    public $nama_mahasiswa;
    public $semester;
    public $tipe_ijin;
    public $alasan_ijin;
    public $lampiran;
    public $file_lampiran;
    public $tujuan_sms;
    public $dosen_wali;
//    public $mahasiswa_id;
//    public $waktu_mulai;
//    public $waktu_selesai;


    /* INI untuk MATAKULIAH IZIN */
    public $matakuliahList;
//    public $tanggal_mulai;
    public $tanggal;

//    public $tanggal_selesai;
//    public $waktu_mulaiKulList;
//    public $waktu_selesaiKulList;
//    public $dosen_matkulList;

    /* OTHER */

//    public  $nimTeman;
//    public  $namaTeman;
    public function rules() {
        return [

            [['tanggal', 'nim', 'nama_mahasiswa', 'tujuan_sms', 'dosen_wali', 'alasan_ijin', 'tipe_ijin',], 'required'],
            [['file_lampiran'], 'file', 'extensions' => 'gif, jpg, png, jpeg'],
            [['nim'], 'string', 'min' => 8, 'max' => 8],
            [['tanggal'], 'string', 'min' => 8, 'skipOnEmpty' => false],
            [['tanggal'], 'checkDate'],
            [['alasan_ijin'], 'string', 'min' => 8, 'max' => 160],
            [['nim', 'nama_mahasiswa', 'tipe_ijin', 'alasan_ijin', 'tujuan_sms',
            'lampiran', 'matakuliahList', 'dosen_wali',
            "sesiList", 'kelas', 'semester', 'tanggal'
                ], 'safe'],
        ];
    }

    public function checkDate($attribute, $params) {

        $tanggal = explode(' ', $this->tanggal);
        $begin = explode(' ', $tanggal[0]);
        $end = explode(' ', $tanggal[3]);
        $fromDate = strtotime($begin[0]);
        $toDate = strtotime($end[0]);

        $dayFrom = (int) date('w', $fromDate);
        $dayTo = (int) date('w', $toDate);


        if ($dayTo == 6 || $dayTo ==0 || $dayFrom==0 || $dayFrom==6) {

           $this->addError($attribute, "Cannot Request On Saturday/Or Sunday");
        }
        
        if(empty ($this->tanggal)) {
            $this->addError($attribute, "Waktu izin Tidak Boleh Kosong");
//            die();
        }
        
    }

}
