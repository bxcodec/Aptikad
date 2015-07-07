<?php

namespace backend\modules\aitk\models;

use Yii;

/**
 * This is the model class for table "aitk_r_matakuliah".
 *
 * @property integer $matakuliah_id
 * @property string $kode_matakuliah
 * @property string $matakuliah
 * @property integer $semester
 * @property integer $deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property AitkDosenmatakuliah[] $aitkDosenmatakuliahs
 * @property AitkRDosen[] $dosens
 * @property AitkMatakuliahizin $aitkMatakuliahizin
 */
class AitkRMatakuliah extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'aitk_r_matakuliah';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['semester', 'deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['kode_matakuliah', 'matakuliah'], 'string', 'max' => 10],
            [['created_by', 'updated_by'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'matakuliah_id' => 'Matakuliah ID',
            'kode_matakuliah' => 'Kode Matakuliah',
            'matakuliah' => 'Matakuliah',
            'semester' => 'Semester',
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
    public function getAitkDosenmatakuliahs()
    {
        return $this->hasMany(AitkDosenmatakuliah::className(), ['matakuliah_id' => 'matakuliah_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDosens()
    {
        return $this->hasMany(AitkRDosen::className(), ['dosen_id' => 'dosen_id'])->viaTable('aitk_dosenmatakuliah', ['matakuliah_id' => 'matakuliah_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAitkMatakuliahizin()
    {
        return $this->hasOne(AitkMatakuliahizin::className(), ['matakuliahizin_id' => 'matakuliah_id']);
    }
}
