<?php

namespace backend\modules\aitk\models;

use Yii;

/**
 * This is the model class for table "aitk_r_asrama".
 *
 * @property integer $asrama_id
 * @property integer $account_id
 * @property string $nama_pengurus
 * @property string $email
 * @property string $handphone
 * @property integer $level_pengurus
 * @property integer $deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property AitkRAccount $account
 * @property AitkRequest[] $aitkRequests
 * @property AitkRequest[] $aitkRequests0
 */
class AitkRAsrama extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'aitk_r_asrama';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id'], 'required'],
            [['account_id', 'level_pengurus', 'deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['nama_pengurus', 'email', 'created_by', 'updated_by'], 'string', 'max' => 50],
            [['handphone'], 'string', 'max' => 16]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'asrama_id' => 'Asrama ID',
            'account_id' => 'Account ID',
            'nama_pengurus' => 'Nama Pengurus',
            'email' => 'Email',
            'handphone' => 'Handphone',
            'level_pengurus' => 'Level Pengurus',
            'deleted' => 'Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(AitkRAccount::className(), ['account_id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAitkRequests()
    {
        return $this->hasMany(AitkRequest::className(), ['pengurus_asrama' => 'asrama_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAitkRequests0()
    {
        return $this->hasMany(AitkRequest::className(), ['tujuan_sms_pengurus' => 'asrama_id']);
    }
}
