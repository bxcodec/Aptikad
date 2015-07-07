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
class FormSendEmail extends Model {

    public $message;
    
    public function rules() {
        return [

            [['message'], 'string', 'min' => 8],
            [['message'], 'safe'],
        ];
    }

}
