<?php

namespace backend\modules\aitk\models;

use Yii;

/**
 * This is the model class for table "aitk_matakuliahizin".
 *
 * @property integer $matakuliahizin_id
 * @property integer $request_id
 * @property integer $matakuliah_id
 * @property integer $dosen_id
 * @property string $waktu_mulai
 * @property string $waktu_selesai
 * @property string $sesi
 * @property integer $deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property AitkRDosen $dosen
 * @property AitkRMatakuliah $matakuliahizin
 * @property AitkRequest $request
 */
class AitkMatakuliahizin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'aitk_matakuliahizin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request_id', 'matakuliah_id', 'dosen_id', 'waktu_mulai', 'waktu_selesai', 'sesi'], 'required'],
            [['request_id', 'matakuliah_id', 'dosen_id', 'deleted'], 'integer'],
            [['waktu_mulai', 'waktu_selesai', 'created_at', 'updated_at'], 'safe'],
            [['sesi'], 'string', 'max' => 1],
            [['created_by', 'updated_by'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'matakuliahizin_id' => 'Matakuliahizin ID',
            'request_id' => 'Request ID',
            'matakuliah_id' => 'Matakuliah ID',
            'dosen_id' => 'Dosen ID',
            'waktu_mulai' => 'Waktu Mulai',
            'waktu_selesai' => 'Waktu Selesai',
            'sesi' => 'Sesi',
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
    public function getDosen()
    {
        return $this->hasOne(AitkRDosen::className(), ['dosen_id' => 'dosen_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatakuliahizin()
    {
        return $this->hasOne(AitkRMatakuliah::className(), ['matakuliah_id' => 'matakuliah_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequest()
    {
        return $this->hasOne(AitkRequest::className(), ['request_id' => 'request_id']);
    }
}
