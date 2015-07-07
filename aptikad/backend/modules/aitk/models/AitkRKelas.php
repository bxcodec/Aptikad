<?php

namespace backend\modules\aitk\models;

use Yii;

/**
 * This is the model class for table "aitk_r_kelas".
 *
 * @property integer $kelas_id
 * @property integer $wali
 * @property string $kode_kelas
 * @property string $nama_kelas
 * @property string $prodi
 * @property integer $deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property AitkRDosen $wali0
 * @property AitkRMahasiswa[] $aitkRMahasiswas
 */
class AitkRKelas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'aitk_r_kelas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wali'], 'required'],
            [['wali', 'deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['kode_kelas'], 'string', 'max' => 7],
            [['nama_kelas', 'prodi'], 'string', 'max' => 25],
            [['created_by', 'updated_by'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelas_id' => 'Kelas ID',
            'wali' => 'Wali',
            'kode_kelas' => 'Kode Kelas',
            'nama_kelas' => 'Nama Kelas',
            'prodi' => 'Prodi',
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
    public function getWali0()
    {
        return $this->hasOne(AitkRDosen::className(), ['dosen_id' => 'wali']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAitkRMahasiswas()
    {
        return $this->hasMany(AitkRMahasiswa::className(), ['kelas_id' => 'kelas_id']);
    }
}
