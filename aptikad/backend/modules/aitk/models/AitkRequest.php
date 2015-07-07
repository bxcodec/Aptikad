<?php

namespace backend\modules\aitk\models;

use Yii;

/**
 * This is the model class for table "aitk_request".
 *
 * @property integer $request_id
 * @property integer $dosen_wali
 * @property integer $requester
 * @property integer $mahasiswa_id
 * @property integer $tujuan_sms_pengurus
 * @property integer $pengurus_asrama
 * @property string $tipe_ijin
 * @property string $waktu_start
 * @property string $waktu_end
 * @property string $alasan_ijin
 * @property string $lampiran
 * @property integer $status_asrama
 * @property string $alasan_penolakan
 * @property integer $status_dosen
 * @property integer $deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property AitkMatakuliahizin[] $aitkMatakuliahizins
 * @property AitkRAsrama $pengurusAsrama
 * @property AitkRMahasiswa $mahasiswa
 * @property AitkRAsrama $tujuanSmsPengurus
 * @property AitkRDosen $dosenWali
 * @property AitkRMahasiswa $requester0
 */
class AitkRequest extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'aitk_request';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
//            [['dosen_wali', 'requester', 'mahasiswa_id', 'tujuan_sms_pengurus', 'pengurus_asrama', 'status_asrama', 'status_dosen', 'deleted'], 'integer'],
            [['waktu_start', 'waktu_end', 'created_at', 'updated_at'], 'safe'],
            [['alasan_ijin'], 'string'],
            [['tipe_ijin'], 'string', 'max' => 30],
            [['lampiran', 'created_by', 'updated_by'], 'string', 'max' => 50],
            [['alasan_penolakan'], 'string', 'max' => 160]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'request_id' => 'Request ID',
            'dosen_wali' => 'Dosen Wali',
            'requester' => 'Requester',
            'mahasiswa_id' => 'Mahasiswa ID',
            'tujuan_sms_pengurus' => 'Tujuan Sms Pengurus',
            'pengurus_asrama' => 'Pengurus Asrama',
            'tipe_ijin' => 'Tipe Ijin',
            'waktu_start' => 'Waktu Start',
            'waktu_end' => 'Waktu End',
            'alasan_ijin' => 'Alasan Ijin',
            'lampiran' => 'Lampiran',
            'status_asrama' => 'Status Asrama',
            'alasan_penolakan' => 'Alasan Penolakan',
            'status_dosen' => 'Status Dosen',
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
    public function getAitkMatakuliahizins() {
        return $this->hasMany(AitkMatakuliahizin::className(), ['request_id' => 'request_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengurusAsrama() {
        return $this->hasOne(AitkRAsrama::className(), ['asrama_id' => 'pengurus_asrama']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMahasiswa() {
        return $this->hasOne(AitkRMahasiswa::className(), ['mahasiswa_id' => 'mahasiswa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTujuanSmsPengurus() {
        return $this->hasOne(AitkRAsrama::className(), ['asrama_id' => 'tujuan_sms_pengurus']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDosenWali() {
        return $this->hasOne(AitkRDosen::className(), ['dosen_id' => 'dosen_wali']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequester0() {
        return $this->hasOne(AitkRMahasiswa::className(), ['mahasiswa_id' => 'requester']);
    }

    public function toDay($int) {
        switch ($int) {

            case 1:
                return "Senin";

            case 2:
                return "Selasa";

            case 3:
                return "Rabu";

            case 4:
                return "Kamis";

            case 5:
                return "Jumat";

            case 6:
                return "Sabtu";

            case 0:
                return "Minggu";
        }
    }

}
