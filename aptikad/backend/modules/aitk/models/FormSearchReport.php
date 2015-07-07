<?php

/*
 *     Name :  Iman Syahputra Situmorang
 *     NIM  :  11113064
 *     Date :  19/May/2015

 */

namespace backend\modules\aitk\models;

use Yii;
use yii\base\Model;

/**
 * Description of FormALasanReject
 *
 * @author Takiya
 */
class FormSearchReport extends Model {

    public $nama_mahasiswa;
    public $kelas;
    public function rules() {
        return [

            [['nama_mahasiswa'], 'string'],
            [['kelas','nama_mahasiswa'], 'safe'],
        ];
    }

}
