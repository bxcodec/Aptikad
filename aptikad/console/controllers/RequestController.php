<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use backend\modules\aitk\models\Inbox;
use backend\modules\aitk\models\Outbox;
use backend\modules\aitk\models\AitkRAsrama;
use backend\modules\aitk\models\AitkRDosen;
use backend\modules\aitk\models\AitkRMahasiswa;
use backend\modules\aitk\models\AitkRequest;
use backend\modules\aitk\models\AitkMatakuliahizin;

/**
 * Test controller
 */
class RequestController extends Controller {

    public function actionIndex() {
        date_default_timezone_set('Asia/Jakarta');

        $now = strtotime(date('h:i:s'));

        usleep(1000000);

        while (strtotime(date('h:i:s')) - $now >= 1) :

            $now = strtotime(date('h:i:s'));


            $inbox = Inbox::findOne(['Processed' => 'false']);


            if (isset($inbox)) :

                $arr = explode(' ', $inbox->TextDecoded);

                switch (strtolower($arr[0])):
                    case "izin":
                        $this->izin($inbox);
                        break;
                    default :
                        $sms = "Format anda SALAH\n Balas IZIN <spasi> YA/TIDAK <spasi> IDENTIFIER";

                        $inbox->Processed = "true";
                        if ($inbox->update()) {
                            if ($this->sendSMS($sms, $inbox->SenderNumber)) {
                                
                            }
                        }
                        break;

                endswitch;

            endif;

            usleep(1000000);

        endwhile;
    }

    public function Approve($arr, $nomor) {

        $nomor = '0' . substr($nomor, 3);

        $asrama = AitkRAsrama::findOne([
                    'handphone' => $nomor,
        ]);
        $dosen = AitkRDosen::findOne([
                    'handphone' => $nomor,
        ]);

        if ((!isset($asrama)) && (!isset($dosen))) {
            $sms = "Maaf Nomor Anda Tidak Terdaftar ";

            if ($this->sendSMS($sms, $nomor)) {
                echo "\n";
            }
        } else {

            $request = AitkRequest::findOne($arr[2]);

            if (isset($request)) :
                if (isset($dosen)) {


                    if ($request->dosen_wali === $dosen->dosen_id) {
                        if (strtolower($arr[1]) === 'ya') {
                            $request->status_dosen = 1;

                            $sms = "Success Accept Request";
                            if ($this->sendSMS($sms, $nomor)) {
                                
                            }
                        }
                        if (strtolower($arr[1]) === 'tidak') {
                            $request->status_dosen = 0;
                            $request->status_asrama = 0;
                            if (array_key_exists(3, $arr)) {
                                $request->alasan_penolakan = $arr[3];
                            }
                            $sms = "Success Reject Request";
                            if ($this->sendSMS($sms, $nomor)) {
                                
                            }
                        }

                        if ($request->save()) {

                            $number = $request->tujuanSmsPengurus->handphone;
                            $nama = $request->mahasiswa->nama_mahasiswa;
                            $nim = $request->mahasiswa->nim;
                            $tempMulai = explode(' ', $request->waktu_start)[1];
                            $tempSelesai = explode(' ', $request->waktu_end)[1];

                            $mulai = substr($tempMulai, 0, 5);
                            $selesai = substr($tempSelesai, 0, 5);


                            $tipeIzin = $request->tipe_ijin == "K" ? "Keluar" : "Tidak Hadir";
                            $id = $request->request_id;
                            $sms = current(explode(" ", $nama)) . "/" . $nim . "/" . $request->mahasiswa->angkatan . " " . $tipeIzin .
                                    " " . $mulai .
                                    "-" . $selesai . " \"" . $request->alasan_ijin . "\" " . "balas 'IZIN YA $id' atau 'IZIN TIDAK $id'";

                            
                            if ($this->sendSMS($sms, $number)) {
                                
                            }
                            
                        } else {
                            echo "Request Error ";
                            print_r($request->getErrors());
                        }
                    } else {
                        $sms = "Maaf Keyword Anda Salah";
                        if ($this->sendSMS($sms, $nomor)) {
                            
                        }
                    }
                }

                if (isset($asrama)) {

                    if (strtolower($arr[1]) === 'ya') {
                        $request->status_asrama = 1;
                    }
                    if (strtolower($arr[1]) === 'tidak') {
                        $request->status_asrama = 0;
                        $request->status_dosen = 0;
                        $alasan_tolak="";
						if (array_key_exists(3, $arr)) {
                            $alasan_tolak = $arr[3];
                        }
                    }

                    $this->Approveasrama($asrama->asrama_id, $request->request_id, $request->status_asrama, $alasan_tolak);

                    $sms = "Success Menyetujui Request";
                    if ($this->sendSMS($sms, $nomor)) {
                        
                    }
                }
            endif;

            if (!isset($request)) {
                $sms = "Format anda SALAH\n Balas IZIN <spasi> YA/TIDAK <spasi> IDENTIFIER";

                if ($this->sendSMS($sms, $nomor)) {
                    
                }
            }
        }
    }

    public function sendSMS($sms, $number) {

        if (empty($number)) {
            return false;
        }
        $kirimsms = new Outbox();
        $kirimsms->TextDecoded = $sms;
        $kirimsms->DestinationNumber = $number;

        $jmlSMS = ceil(strlen($sms) / 153);

        if ($jmlSMS > 1)
            $kirimsms->MultiPart = "true";
        else
            $kirimsms->MultiPart = "false";

        $kirimsms->CreatorID = "Gammu";
        if ($kirimsms->save()) {
            
        } else {

            print_r($kirimsms->getErrors());
        }

        return true;
    }

    public function Approveasrama($idasrama, $id, $value, $alasan_tolak) {

        $model = AitkRequest::findOne($id);
        $asrama = AitkRAsrama::findOne($idasrama);
        $model->status_asrama = $value;
		$model->status_dosen = $value;
		$model->alasan_penolakan=$alasan_tolak;
        $model->pengurus_asrama = $asrama->asrama_id;

        if ($model->save()) {

            if (strtolower($model->tipe_ijin) == "tidak hadir")
                $this->InsertMatakuliahizin($model->request_id);
        }
    }

    public function InsertMatakuliahizin($request_id) {



        $allIzin = AitkTemptable::findAll(['request_id' => $request_id]);
        $rows = array();
        foreach ($allIzin as $izin) {

            $rows [] = [
                'matakuliah_id' => $izin->matakuliah_id,
                'sesi' => $izin->sesi,
                'request_id' => $izin->request_id,
            ];
        }
        Yii::$app->db->createCommand()->batchInsert(AitkMatakuliahizin::tableName(), [
            'matakuliah_id',
            'sesi',
            'request_id',
                ], $rows)->execute();
    }

    public function izin($inbox) {

        $nomor = $inbox->SenderNumber;
        $inbox->Processed = "true";

        $arr = explode(' ', $inbox->TextDecoded);

        switch (strtolower($arr[1])) :
            case "ya":
                if ($inbox->update()) {
                    $sms = "";
                    if (count($arr) == 3) {
                        $this->Approve($arr, $nomor);
                    } else {
                        $sms = "Maaf Keyword Anda Salah";
                        if ($this->sendSMS($sms, $inbox->SenderNumber)) {
                            
                        }
                    }
                } else {
                    echo print_r($inbox->getErrors());
                }
                break;
            case "tidak":
                if ($inbox->update()) {
                    $sms = "";
                    if (count($arr) >= 3) {

                        $message = "";
                        $j = 0;
                        for ($j = 0; $j < count($arr); $j++) {
                            if ($j > 2) {
                                $message.=$arr[$j] . " ";
                                $arr[$j] = "";
                            }
                        }
                        $arr[3] = $message;
                        $this->Approve($arr, $nomor);
                    } else {
                        $sms = "Maaf Keyword Anda Salah";
                        if ($this->sendSMS($sms, $inbox->SenderNumber)) {
                            continue;
                        }
                    }
                } else {
                    echo print_r($inbox->getErrors());
                }

                break;
            default:
                $sms = "Maaf Keyword yang anda masukkan salah";
                if ($this->sendSMS($sms, $inbox->SenderNumber)) {
                    
                }
                break;
        endswitch;
    }

}

?>