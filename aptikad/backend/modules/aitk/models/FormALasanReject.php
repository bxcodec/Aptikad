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
class FormALasanReject extends Model {

    public $alasan_penolakan;

    public function rules() {
        return [

            [['alasan_penolakan'], 'string', 'min' => 8, 'max' => 160],
            [['alasan_penolakan'], 'safe'],
        ];
    }

}
