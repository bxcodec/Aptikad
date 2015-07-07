<?php

namespace backend\modules\aitk\models;

use Yii;

/**
 * This is the model class for table "aitk_r_mahasiswa".
 *
 * @property integer $mahasiswa_id
 * @property integer $account_id
 * @property integer $kelas_id
 * @property string $nim
 * @property string $nama_mahasiswa
 * @property integer $semester
 * @property string $email
 * @property string $handphone
 * @property integer $deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property AitkRKelas $kelas
 * @property AitkRAccount $account
 * @property AitkRequest[] $aitkRequests
 * @property AitkRequest[] $aitkRequests0
 */
class AitkRMahasiswa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'aitk_r_mahasiswa';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'kelas_id'], 'required'],
            [['account_id', 'kelas_id', 'semester', 'deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['nim'], 'string', 'max' => 8],
            [['nama_mahasiswa', 'email', 'created_by', 'updated_by'], 'string', 'max' => 50],
            [['handphone'], 'string', 'max' => 16]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mahasiswa_id' => 'Mahasiswa ID',
            'account_id' => 'Account ID',
            'kelas_id' => 'Kelas ID',
            'nim' => 'Nim',
            'nama_mahasiswa' => 'Nama Mahasiswa',
            'semester' => 'Semester',
            'email' => 'Email',
            'handphone' => 'Handphone',
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
    public function getKelas()
    {
        return $this->hasOne(AitkRKelas::className(), ['kelas_id' => 'kelas_id']);
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
        return $this->hasMany(AitkRequest::className(), ['mahasiswa_id' => 'mahasiswa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAitkRequests0()
    {
        return $this->hasMany(AitkRequest::className(), ['requester' => 'mahasiswa_id']);
    }
}
