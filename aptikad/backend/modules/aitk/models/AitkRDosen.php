<?php

namespace backend\modules\aitk\models;

use Yii;

/**
 * This is the model class for table "aitk_r_dosen".
 *
 * @property integer $dosen_id
 * @property integer $account_id
 * @property string $nama_dosen
 * @property string $email
 * @property string $handphone
 * @property integer $iswali
 * @property integer $deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property AitkDosenmatakuliah[] $aitkDosenmatakuliahs
 * @property AitkRMatakuliah[] $matakuliahs
 * @property AitkMatakuliahizin[] $aitkMatakuliahizins
 * @property AitkRAccount $account
 * @property AitkRKelas[] $aitkRKelas
 * @property AitkRequest[] $aitkRequests
 */
class AitkRDosen extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'aitk_r_dosen';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id'], 'required'],
            [['account_id', 'iswali', 'deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['nama_dosen', 'email', 'created_by', 'updated_by'], 'string', 'max' => 50],
            [['handphone'], 'string', 'max' => 16]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dosen_id' => 'Dosen ID',
            'account_id' => 'Account ID',
            'nama_dosen' => 'Nama Dosen',
            'email' => 'Email',
            'handphone' => 'Handphone',
            'iswali' => 'Iswali',
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
        return $this->hasMany(AitkDosenmatakuliah::className(), ['dosen_id' => 'dosen_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatakuliahs()
    {
        return $this->hasMany(AitkRMatakuliah::className(), ['matakuliah_id' => 'matakuliah_id'])->viaTable('aitk_dosenmatakuliah', ['dosen_id' => 'dosen_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAitkMatakuliahizins()
    {
        return $this->hasMany(AitkMatakuliahizin::className(), ['dosen_id' => 'dosen_id']);
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
    public function getAitkRKelas()
    {
        return $this->hasMany(AitkRKelas::className(), ['wali' => 'dosen_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAitkRequests()
    {
        return $this->hasMany(AitkRequest::className(), ['dosen_wali' => 'dosen_id']);
    }
}
