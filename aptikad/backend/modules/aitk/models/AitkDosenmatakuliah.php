<?php

namespace backend\modules\aitk\models;

use Yii;

/**
 * This is the model class for table "aitk_dosenmatakuliah".
 *
 * @property integer $dosen_id
 * @property integer $matakuliah_id
 * @property integer $deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string $deleted_by
 *
 * @property AitkRDosen $dosen
 * @property AitkRMatakuliah $matakuliah
 */
class AitkDosenmatakuliah extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'aitk_dosenmatakuliah';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dosen_id', 'matakuliah_id'], 'required'],
            [['dosen_id', 'matakuliah_id', 'deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'deleted_by'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dosen_id' => 'Dosen ID',
            'matakuliah_id' => 'Matakuliah ID',
            'deleted' => 'Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'deleted_by' => 'Deleted By',
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
    public function getMatakuliah()
    {
        return $this->hasOne(AitkRMatakuliah::className(), ['matakuliah_id' => 'matakuliah_id']);
    }
}
