<?php

namespace backend\modules\aitk\models;

use Yii;

/**
 * This is the model class for table "aitk_temptable".
 *
 * @property integer $temp_id
 * @property integer $request_id
 * @property integer $matakuliah_id
 * @property string $sesi
 * @property integer $deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property AitkRMatakuliah $matakuliah
 * @property AitkRequest $request
 */
class AitkTemptable extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'aitk_temptable';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request_id', 'matakuliah_id'], 'required'],
            [['request_id', 'matakuliah_id', 'deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
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
            'temp_id' => 'Temp ID',
            'request_id' => 'Request ID',
            'matakuliah_id' => 'Matakuliah ID',
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
    public function getMatakuliah()
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
