<?php

namespace backend\modules\aitk\models;

use Yii;

/**
 * This is the model class for table "aitk_r_account".
 *
 * @property integer $account_id
 * @property string $username
 * @property string $password
 * @property integer $level
 * @property integer $deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property AitkRAsrama[] $aitkRAsramas
 * @property AitkRDosen[] $aitkRDosens
 * @property AitkRMahasiswa[] $aitkRMahasiswas
 */
class AitkRAccount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'aitk_r_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['level', 'deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['username'], 'string', 'max' => 10],
            [['password'], 'string', 'max' => 250],
            [['created_by', 'updated_by'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'account_id' => 'Account ID',
            'username' => 'Username',
            'password' => 'Password',
            'level' => 'Level',
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
    public function getAitkRAsramas()
    {
        return $this->hasMany(AitkRAsrama::className(), ['account_id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAitkRDosens()
    {
        return $this->hasMany(AitkRDosen::className(), ['account_id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAitkRMahasiswas()
    {
        return $this->hasMany(AitkRMahasiswa::className(), ['account_id' => 'account_id']);
    }
}
