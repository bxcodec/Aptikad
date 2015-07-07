<?php

namespace backend\modules\aitk\controllers;

use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use Yii;
use yii\helpers\Html;
use backend\modules\aitk\models\AitkRequest;
use backend\modules\aitk\models\search\AitkRequestSearch;
use backend\modules\aitk\models\search\AitkRequestSearchReport;
use backend\modules\aitk\models\search\AitkRequestSearchDw;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\modules\aitk\models\FormIzin;
use backend\modules\aitk\models\AitkRMahasiswa;
use backend\modules\aitk\models\AitkRKelas;
use backend\modules\aitk\models\AitkRAsrama;
use backend\modules\aitk\models\AitkRDosen;
use backend\modules\aitk\models\AitkRMatakuliah;
use backend\modules\aitk\models\AitkMatakuliahizin;
use backend\modules\aitk\models\AitkTemptable;
use backend\modules\aitk\models\FormALasanReject;
use backend\modules\aitk\models\FormSendEmail;
use backend\modules\aitk\models\Outbox;
use backend\modules\aitk\models\OutboxMultipart;
use backend\modules\aitk\models\Inbox;
use yii\web\Response;
use yii\db\Query;
use mPDF;

/**
 * RequestController implements the CRUD actions for AitkRequest model.
 */
class RequestController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all AitkRequest models.
     * @return mixed
     */
    public function actionGetmahasiswa($name) {
        Yii::$app->response->format = Response::FORMAT_JSON;


        $nama = current(explode('(', $name));
        $mahasiswa = AitkRMahasiswa::find()->where(['nama_mahasiswa' => $nama])->one();
        $matakuliah = AitkRMatakuliah::findAll(['semester' => $mahasiswa->semester]);

        $arrmahasiswa [] = array(
            'nim' => $mahasiswa->nim,
            'nama_mahasiswa' => $mahasiswa->nama_mahasiswa,
            'kelas' => $mahasiswa->kelas->kode_kelas,
            'wali' => $mahasiswa->kelas->wali0->nama_dosen,
            'semester' => $mahasiswa->semester,
            'matakuliah' => $matakuliah,
        );


        return $arrmahasiswa;
    }

    public function actionMahasiswalist($q = null) {
        $query = new Query;

        $query->select('nama_mahasiswa, nim')
                ->from('aitk_r_mahasiswa')
                ->where('nama_mahasiswa LIKE "%' . $q . '%"')
                ->orderBy('nama_mahasiswa');
        $command = $query->createCommand();
        $data = $command->queryAll();
        $out = [];
        foreach ($data as $d) {
            $out[] = ['value' => $d['nama_mahasiswa'] . '(' . $d['nim'] . ')'];
        }

        echo Json::encode($out);
    }

    public function actionIndex() {
        $searchModel = new AitkRequestSearch();
        $mahasiswa = AitkRMahasiswa::findOne(['account_id' => Yii::$app->user->id]);

        /* HAPUS INI UNTUK PENGGUNAAN RBAC */
        /*         * **** */
        if (!isset($mahasiswa)) {
            $this->redirect(Yii::$app->homeUrl);
        }
        /*         * **** */

        $searchModel->mahasiswa_id = $mahasiswa->nama_mahasiswa;
        /* ALL REQUEST */
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        /* PENDING */
        $dataProviderPending = new ActiveDataProvider([
            'query' => AitkRequest::find()->where([
                'mahasiswa_id' => $mahasiswa->mahasiswa_id,
                'status_dosen' => NULL,
            ])->orWhere([
                'status_asrama' => NULL,
                'mahasiswa_id' => $mahasiswa->mahasiswa_id,
                'status_dosen' => 1
            ])
            ,
            'sort' => ['defaultOrder' => ['request_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 3,
            ],
        ]);
        /* Approved */

        $dataProviderApproved = new ActiveDataProvider([
            'query' => AitkRequest::find()->where([
                'mahasiswa_id' => $mahasiswa->mahasiswa_id,
                'status_dosen' => 1,
                'status_asrama' => 1,
            ]),
            'sort' => ['defaultOrder' => ['request_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 3,
            ],
        ]);
        $dataProviderRejected = new ActiveDataProvider([
            'query' => AitkRequest::find()->where([
                'mahasiswa_id' => $mahasiswa->mahasiswa_id,
                'status_dosen' => 0,
            ])->orWhere([
                'mahasiswa_id' => $mahasiswa->mahasiswa_id,
                'status_asrama' => 0,
            ]),
            'sort' => ['defaultOrder' => ['request_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 3,
            ],
        ]);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'dataProviderApproved' => $dataProviderApproved,
                    'dataProviderRejected' => $dataProviderRejected,
                    'dataProviderPending' => $dataProviderPending,
        ]);
    }

    /**
     * Displays a single AitkRequest model.
     * @param integer $id
     * @return mixed
     */
    public function actionDetail($id) {

        $dataProviderMatkulIzin = new ActiveDataProvider([
            'query' => AitkMatakuliahizin::find()->where([
                'request_id' => $id,
            ]),
            'sort' => ['defaultOrder' => ['matakuliahizin_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        $filled = AitkMatakuliahizin::findOne(['request_id' => $id]);

        return $this->renderAjax('detail', [
                    'model' => $this->findModel($id),
                    'matkulIzin' => $dataProviderMatkulIzin,
                    'filled' => $filled
        ]);
    }

    /**
     * Creates a new AitkRequest model.
     * If creation is successful, the browser will be redirected to the 'detail' page.
     * @return mixed
     */
    public function actionAdd() {
        $model = new FormIzin();
        $asrama = \backend\modules\aitk\models\AitkRAsrama::findOne(['account_id' => Yii::$app->user->id]);
        /* HAPUS INI UNTUK PENGGUNAAN RBAC */
        /*         * **** */
        if (!isset($asrama)) {
            $this->redirect(Yii::$app->homeUrl);
        }
        /*         * **** */
        if ($model->load(Yii::$app->request->post())) {


            $nama = current(explode('(', $model->nama_mahasiswa));
            $mahasiswa = AitkRMahasiswa::findOne(['nama_mahasiswa' => $nama]);

            if (!isset($mahasiswa)) {
                Yii::$app->getSession()->setFlash('danger', [
                    'type' => 'danger',
                    'delay' => 50000,
                    'icon' => 'glyphicon glyphicon-warning-sign',
                    'message' => 'Isi nama mahasiswa pada field tersedia',
                    'title' => 'Mahasiswa ' . $nama . ' Tidak ditemukan',
                ]);
                return $this->redirect(['asrama']);
            }
            $request = new AitkRequest();
            $model->file_lampiran = UploadedFile::getInstance($model, 'file_lampiran');
            $currdate = date("Y-m-d");
            $nama_file = "";
            if ($model->file_lampiran) {
                if (empty($model->lampiran)) {
                    Yii::$app->getSession()->setFlash('warning', [
                        'type' => 'warning',
                        'delay' => 50000,
                        'icon' => 'glyphicon glyphicon-warning-sign',
                        'message' => 'Jika memiliki photo lampiran, pastikan isi kode surat/lampiran tersebut',
                        'title' => 'Lampiran salah',
                    ]);
                    return $this->redirect(['asrama']);
                }

                $nama_file = $currdate . $mahasiswa->nim . current(explode(' ', $model->lampiran)) . '.' . $model->file_lampiran->extension;
                $model->file_lampiran->saveAs('file_lampiran/' . $nama_file);
            }
            $tanggal = explode(' ', $model->tanggal);

            if (!empty($model->tanggal)) {

                $request->alasan_ijin = $model->alasan_ijin;
                $request->dosen_wali = $mahasiswa->kelas->wali0->dosen_id;
                $request->mahasiswa_id = $mahasiswa->mahasiswa_id;
                $request->waktu_start = $tanggal[0] . ' ' . $tanggal[1];
                $request->waktu_end = $tanggal[3] . ' ' . $tanggal[4];
                $request->lampiran = $model->lampiran;
                $request->file_lampiran = $nama_file;
                $request->tipe_ijin = 'Tidak Hadir';
                $request->status_asrama = 1;
                $request->status_dosen = 1;
                $request->pengurus_asrama = $asrama->asrama_id;

                if ($request->save()) {
                    Yii::$app->getSession()->setFlash('success', [
                        'type' => 'success',
                        'delay' => 50000,
                        'icon' => 'glyphicon glyphicon-ok',
                        'message' => 'Data Saved, See the result in Approved Request',
                        'title' => 'Successfull to add Request',
                    ]);
                    return $this->redirect(['asrama']);
                } else {
                    Yii::$app->getSession()->setFlash('danger', [
                        'type' => 'danger',
                        'delay' => 50000,
                        'icon' => 'glyphicon glyphicon-exclamation-sign',
                        'message' => 'Cannot Save Data',
                        'title' => 'Internal Server Error',
                    ]);
                    return $this->redirect(['asrama']);
                }
            } else {
                Yii::$app->getSession()->setFlash('danger', [
                    'type' => 'danger',
                    'delay' => 50000,
                    'icon' => 'glyphicon glyphicon-warning-sign',
                    'message' => 'Tolong Field Waktu Izin Di Isi',
                    'title' => 'Request Tidak di Save',
                ]);
                return $this->redirect(['asrama']);
            }
        } else {
            return $this->renderAjax('addIzin', [
                        'model' => $model,
            ]);
        }
    }

    /**
      var @count int $name Description
     *      */
    public function actionRequestizin() {

        $model = new FormIzin();
        $idUser = Yii::$app->user->id;
        $mahasiswaLogin = AitkRMahasiswa::find()->where(
                        ['account_id' => $idUser]
                )->one();
        /* HAPUS INI UNTUK PENGGUNAAN RBAC */
        /*         * **** */
        if (!isset($mahasiswaLogin)) {
            $this->redirect(Yii::$app->homeUrl);
        }
        /*         * **** */
        $semester = $mahasiswaLogin->semester;
        $jurusan = $mahasiswaLogin->jurusan;
        $count = AitkRMatakuliah::find()
                ->where(['semester' => $semester])->andWhere(['jurusan' => NULL])->orWhere(['jurusan' => $jurusan])
                ->count();

        $allMatakuliah = AitkRMatakuliah::find()
                ->where(['semester' => $semester])->andWhere(['jurusan' => NULL])->orWhere(['jurusan' => $jurusan])
                ->orderBy('matakuliah_id')
                ->all();
        $matkulId = array();
        $arrMatkul = array();
        foreach ($allMatakuliah as $valueMatkul => $key) {
            foreach ($key as $val => $isi)
                if ($val == "alias") {
                    $arrMatkul [] = $key[$val];
                }
        }

        foreach ($allMatakuliah as $valueMatkul => $key) {
            foreach ($key as $val => $isi)
                if ($val == "matakuliah_id") {
                    $matkulId[] = $key[$val];
                }
        }

        $model->nama_mahasiswa = $mahasiswaLogin->nama_mahasiswa;
        $model->nim = $mahasiswaLogin->nim;
        $model->kelas = $mahasiswaLogin->kelas->kode_kelas;
        $model->semester = $mahasiswaLogin->semester;
        $model->dosen_wali = $mahasiswaLogin->kelas->wali0->nama_dosen;

        if ($model->load(Yii::$app->request->post())) {


            if (!$model->validate()) {
                return $this->render('requestIzin', [
                            'model' => $model,
                            'count' => $count,
                            'arrMatkul' => $arrMatkul,
                            'matkulId' => $matkulId,
                            'mahasiswaLogin' => $mahasiswaLogin,
                ]);
            }



            $model->file_lampiran = UploadedFile::getInstance($model, 'file_lampiran');

            $currdate = date("Y-m-d");

            $nama_file = "";
            if ($model->file_lampiran) {

                if (empty($model->lampiran)) {
                    Yii::$app->getSession()->setFlash('warning', [
                        'type' => 'warning',
                        'delay' => 50000,
                        'icon' => 'glyphicon glyphicon-warning-sign',
                        'message' => 'Jika memiliki photo lampiran, pastikan isi kode surat/lampiran tersebut',
                        'title' => 'Lampiran salah',
                    ]);
                    return $this->redirect(['requestizin']);
                }

                $nama_file = $currdate . $mahasiswaLogin->nim . current(explode(' ', $model->lampiran)) . '.' . $model->file_lampiran->extension;
                $model->file_lampiran->saveAs('file_lampiran/' . $nama_file);
            }

            $tanggal = explode(' ', $model->tanggal);

            if (!empty($model->tanggal)) {

                $request = new AitkRequest();
                $request->alasan_ijin = $model->alasan_ijin;
                $request->dosen_wali = $mahasiswaLogin->kelas->wali0->dosen_id;
                $request->mahasiswa_id = $mahasiswaLogin->mahasiswa_id;
                $request->tujuan_sms_pengurus = $model->tujuan_sms;
                $request->lampiran = $model->lampiran;
                $request->requester = $mahasiswaLogin->mahasiswa_id;
                $request->tipe_ijin = strtolower($model->tipe_ijin) == 'k' ? 'Keluar' : (strtolower($model->tipe_ijin) == 's' ? 'Tidak Hadir' : '');
                $request->file_lampiran = $nama_file;
                $request->waktu_start = $tanggal[0] . ' ' . $tanggal[1];
                $request->waktu_end = $tanggal[3] . ' ' . $tanggal[4];


                if ($request->save()) {

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

                    $this->sendSMS($sms, $mahasiswaLogin->kelas->wali0->handphone);

                    $status = count($model->matakuliahList);

//                    echo $status;
//                    die();
                    if (strtolower($model->tipe_ijin) == 's') {
                        if ($status > 0)
                            $this->InsertToTemp($model, $request->request_id);
                    }


                    return $this->redirect(['index']);
                }
                else {
                    Yii::$app->getSession()->setFlash('danger', [
                        'type' => 'danger',
                        'delay' => 50000,
                        'icon' => 'glyphicon glyphicon-exclamation-sign',
                        'message' => 'Tidak Dapat Menyimpan Data',
                        'title' => 'Error Saat Menyimpan Data',
                    ]);
//
                    return $this->redirect(['requestizin']);
                }
            } else {
                Yii::$app->getSession()->setFlash('warning', [
                    'type' => 'warning',
                    'delay' => 50000,
                    'icon' => 'glyphicon glyphicon-warning-sign',
                    'message' => 'Tolong Field Waktu Izin Di Isi',
                    'title' => 'Waktu Izin Tidak di Isi',
                ]);
                return $this->redirect(['requestizin']);
            }

//            }
        } else {
            return $this->render('requestIzin', [
                        'model' => $model,
                        'count' => $count,
                        'arrMatkul' => $arrMatkul,
                        'matkulId' => $matkulId,
                        'mahasiswaLogin' => $mahasiswaLogin,
            ]);
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

    public function InsertToTemp($model, $request_id) {
        $j = 0;
        $rows = array();
        $sesiArr = array_values(array_filter($model->sesiList));

        if (count($sesiArr) > 0) :
            foreach ($sesiArr as $sesi) {
                $i = 0;

                foreach ($sesi as $key => $val) {
                    $rows [] = [
                        'matakuliah_id' => $model->matakuliahList[$j],
                        'sesi' => current(explode('_', $val)),
                        'request_id' => $request_id,
                    ];
                }

                $j++;
            }

        endif;
        if (count($sesiArr) <= 0):

            foreach ($model->matakuliahList as $nilai) {
                $rows [] = [
                    'matakuliah_id' => $nilai,
                    'sesi' => null,
                    'request_id' => $request_id,
                ];
            }
        endif;


        Yii::$app->db->createCommand()->batchInsert(AitkTemptable::tableName(), [
            'matakuliah_id',
            'sesi',
            'request_id',
                ], $rows)->execute();
    }

    public function actionDosen() {

        $dosen = AitkRDosen::findOne(['account_id' => Yii::$app->user->id]);
        /* HAPUS INI UNTUK PENGGUNAAN RBAC */
        /*         * **** */
        if (!isset($dosen)) {
            throw new \yii\web\HttpException(403, 'You not authorized to enter this', 405);
        }
        if (Yii::$app->user->isGuest)
            $this->redirect(Yii::$app->homeUrl);


        /*         * **** */

        $matkul = \backend\modules\aitk\models\AitkDosenmatakuliah::findAll(['dosen_id' => $dosen->dosen_id]);
        $arrMtaId = array();
        foreach ($matkul as $mta) {

            $arrMtaId[] = $mta["matakuliah_id"];
        }

        $allIzin = AitkMatakuliahizin::find()->where([
                    "matakuliah_id" => $arrMtaId
                ])->all();

        
        


        $searchModel = new AitkRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('dosen', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'dosen' => $dosen,
                    'allIzin'=>$allIzin
        ]);
    }

    public function actionBaak($nim = null, $kelas_id = null) {

        if (Yii::$app->user->isGuest)
            $this->redirect(Yii::$app->homeUrl);

        $akun = \common\models\AitkRAccount::findOne(Yii::$app->user->id);
        /* HAPUS INI UNTUK PENGGUNAAN RBAC */
        /*         * **** */ 
        if ($akun->username !== 'baakitdel') {
            throw new \yii\web\HttpException(403, 'You not authorized to enter this', 405);
        }

        /*         * **** */
        $searchModel = new AitkRequestSearchReport();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        $model = new \backend\modules\aitk\models\FormSearchReport();
        $nama = "";
        $kelas = null;
        $mhs = null;
        $matakuliah = array();
        $arrMatakuliah = null;
        $arrMatakuliahId = array();
        $totalTeori = array();
        $totalPrak = array();
        $totalLainnya = array();
        $mahasiswaAll = array();

        $Tipe = array("Tidak Hadir", "Keluar Kampus");

//        
//        if(isset( $kelas)) {
//             $mahasiswaAll = AitkRMahasiswa::findAll(["kelas_id" => $kelas->kelas_id]);
//          
//        }
        if (isset($nim)) {
            $mhs = AitkRMahasiswa::findOne(['nim' => $nim, 'kelas_id' => $kelas_id]);

            if (isset($mhs)) {
                $class = AitkRKelas::findOne($kelas_id);

                $dataProvider = new ActiveDataProvider([
                    'query' => AitkRequest::find()->where([
                        'status_dosen' => 1,
                        'status_asrama' => 1,
                        'dosen_wali' => $class->wali,
                        'mahasiswa_id' => $mhs->mahasiswa_id
                    ]),
                    'pagination' => [
                        'pageSize' => 3,
                    ],
                    'sort' => ['defaultOrder' => ['request_id' => SORT_DESC]],
                ]);

                $kelas = null;
                $arr = $this->MahasiswaReport($mhs);
                $totalTeori = $arr["totalTeori"];
                $totalPrak = $arr["totalPrak"];
                $totalLainnya = $arr["totalLainnya"];
                $tidkHadir = $arr["tidkHadir"];
                $keluar = $arr["keluar"];
                $arrMatakuliah = $arr["arrMatakuliah"];
                $totalIjin = array($tidkHadir, $keluar);
                return $this->render('bkreport_one', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'model' => $model,
                            'mahasiswa' => $mhs,
                            'kelas' => $kelas,
                            'arrMatakuliah' => $arrMatakuliah,
                            'totalTeori' => $totalTeori,
                            'totalPrak' => $totalPrak,
                            'totalLainnya' => $totalLainnya,
                            'Tipe' => $Tipe,
                            'totalIzin' => $totalIjin,
                            'mahasiswaAll' => $mahasiswaAll
                ]);
            } else {
                throw new \yii\web\HttpException(404, 'Student is not From this Class', 404);
            }
        }


        if ($model->load(Yii::$app->request->post())) {
            $nama = current(explode('(', $model->nama_mahasiswa));
            $kelas = AitkRKelas::findOne($model->kelas);
            $tidkHadir = 0;
            $keluar = 0;

            $mhs = AitkRMahasiswa::findOne(['nama_mahasiswa' => $nama]);
            if (isset($kelas)) {

                $sampleMhs = AitkRMahasiswa::findOne(['kelas_id' => $kelas->kelas_id]);

                $matakuliah = AitkRMatakuliah::find()->where(['semester' => $sampleMhs->semester])->andWhere(['jurusan' => NULL])->orWhere(['jurusan' => $sampleMhs->jurusan])->all();

                foreach ($matakuliah as $kul) {
                    $arrMatakuliah[] = $kul["alias"];
                }

                $mahasiswaAll = AitkRMahasiswa::findAll(["kelas_id" => $kelas->kelas_id]);
                $arr = array();
                $arrTotalTeoriKelas = array();
                $arrTotalPrakKelas = array();
                $arrTotalLainnyaKelas = array();
                $arrTotalTidakHadirKelas = array();
                $arrTotalKeluarKampusKelas = array();

                foreach ($mahasiswaAll as $mahasiswa) {
                    $arr[] = $this->MahasiswaReport($mahasiswa);
                    $arrTotalTeoriKelas[] = $this->MahasiswaReport($mahasiswa)["totalTeori"];
                    $arrTotalPrakKelas[] = $this->MahasiswaReport($mahasiswa)["totalPrak"];
                    $arrTotalLainnyaKelas[] = $this->MahasiswaReport($mahasiswa)["totalLainnya"];
                    $arrTotalTidakHadirKelas[] = $this->MahasiswaReport($mahasiswa)["tidkHadir"];
                    $arrTotalKeluarKampusKelas[] = $this->MahasiswaReport($mahasiswa)["keluar"];
                }


                $totalTeori = $this->sumArrayValues($arrTotalTeoriKelas);
                $totalPrak = $this->sumArrayValues($arrTotalPrakKelas);
                $totalLainnya = $this->sumArrayValues($arrTotalLainnyaKelas);
                $tidkHadir = array_sum($arrTotalTidakHadirKelas);
                $keluar = array_sum($arrTotalKeluarKampusKelas);
            }

            if (isset($mhs)) {

                $arr = $this->MahasiswaReport($mhs);
                $totalTeori = $arr["totalTeori"];
                $totalPrak = $arr["totalPrak"];
                $totalLainnya = $arr["totalLainnya"];
                $tidkHadir = $arr["tidkHadir"];
                $keluar = $arr["keluar"];
                $arrMatakuliah = $arr["arrMatakuliah"];
//                $Tipe = array("Tidak Hadir", "Keluar Kampus");

                $totalIjin = array($tidkHadir, $keluar);

                $dataProvider = new ActiveDataProvider([
                    'query' => AitkRequest::find()->where([
                        'status_dosen' => 1,
                        'status_asrama' => 1,
//                        'dosen_wali' => $class->wali,
                        'mahasiswa_id' => $mhs->mahasiswa_id
                    ]),
                    'pagination' => [
                        'pageSize' => 3,
                    ],
                    'sort' => ['defaultOrder' => ['request_id' => SORT_DESC]],
                ]);
                return $this->render('baak', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'model' => $model,
                            'mahasiswa' => $mhs,
                            'kelas' => $kelas,
                            'arrMatakuliah' => $arrMatakuliah,
                            'mahasiswaAll' => $mahasiswaAll,
                            'totalTeori' => $totalTeori,
                            'totalPrak' => $totalPrak,
                            'totalLainnya' => $totalLainnya,
                            'Tipe' => $Tipe,
                            'totalIzin' => $totalIjin,
                ]);
            }


            $Tipe = array("Tidak Hadir", "Keluar Kampus");

            $totalIjin = array($tidkHadir, $keluar);


//            $model->nama_mahasiswa = $model->nama_mahasiswa;
            return $this->render('baak', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'model' => $model,
                        'mahasiswa' => $mhs,
                        'kelas' => $kelas,
                        'arrMatakuliah' => $arrMatakuliah,
                        'mahasiswaAll' => $mahasiswaAll,
                        'totalTeori' => $totalTeori,
                        'totalPrak' => $totalPrak,
                        'totalLainnya' => $totalLainnya,
                        'Tipe' => $Tipe,
                        'totalIzin' => $totalIjin,
            ]);
        } else {
            return $this->render('baak', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'model' => $model,
            ]);
        }
    }

    public function actionReportdw($nim = null) {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['index']);
        }
        $idUser = Yii::$app->user->id;

        $dosen = AitkRDosen::findOne(['account_id' => $idUser]);
        $kelas = AitkRKelas::findOne(["wali" => $dosen->dosen_id]);

        $searchModel = new AitkRequestSearchDw();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $model = new \backend\modules\aitk\models\FormSearchReport();
        $matakuliah = array();
        $arrMatakuliah = null;
        $arrMatakuliahId = array();
        $totalTeori = array();
        $totalPrak = array();
        $totalLainnya = array();
        $mahasiswaAll = array();
        $Tipe = array("Tidak Hadir", "Keluar Kampus");
        if (isset($nim)) {
            $mhs = AitkRMahasiswa::findOne(['nim' => $nim, 'kelas_id' => $kelas->kelas_id]);

            if (isset($mhs)) {
                $dataProvider = new ActiveDataProvider([
                    'query' => AitkRequest::find()->where([
                        'status_dosen' => 1,
                        'status_asrama' => 1,
                        'dosen_wali' => $dosen->dosen_id,
                        'mahasiswa_id' => $mhs->mahasiswa_id
                    ]),
                    'pagination' => [
                        'pageSize' => 3,
                    ],
                    'sort' => ['defaultOrder' => ['request_id' => SORT_DESC]],
                ]);

                $kelas = null;
                $arr = $this->MahasiswaReport($mhs);
                $totalTeori = $arr["totalTeori"];
                $totalPrak = $arr["totalPrak"];
                $totalLainnya = $arr["totalLainnya"];
                $tidkHadir = $arr["tidkHadir"];
                $keluar = $arr["keluar"];
                $arrMatakuliah = $arr["arrMatakuliah"];
                $totalIjin = array($tidkHadir, $keluar);
                return $this->render('dwreport', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'model' => $model,
                            'mahasiswa' => $mhs,
                            'kelas' => $kelas,
                            'arrMatakuliah' => $arrMatakuliah,
                            'totalTeori' => $totalTeori,
                            'totalPrak' => $totalPrak,
                            'totalLainnya' => $totalLainnya,
                            'Tipe' => $Tipe,
                            'totalIzin' => $totalIjin,
                            'mahasiswaAll' => $mahasiswaAll
                ]);
            } else {
                throw new \yii\web\HttpException(404, 'Student is not From this Class', 404);
            }
        }

        if (isset($kelas)) {

            $sampleMhs = AitkRMahasiswa::findOne(['kelas_id' => $kelas->kelas_id]);

            $matakuliah = AitkRMatakuliah::find()->where(['semester' => $sampleMhs->semester])->andWhere(['jurusan' => NULL])->orWhere(['jurusan' => $sampleMhs->jurusan])->all();

            foreach ($matakuliah as $kul) {
                $arrMatakuliah[] = $kul["alias"];
            }

            $mahasiswaAll = AitkRMahasiswa::findAll(["kelas_id" => $kelas->kelas_id]);
            $arr = array();
            $arrTotalTeoriKelas = array();
            $arrTotalPrakKelas = array();
            $arrTotalLainnyaKelas = array();
            $arrTotalTidakHadirKelas = array();
            $arrTotalKeluarKampusKelas = array();

            foreach ($mahasiswaAll as $mahasiswa) {
                $arr[] = $this->MahasiswaReport($mahasiswa);
                $arrTotalTeoriKelas[] = $this->MahasiswaReport($mahasiswa)["totalTeori"];
                $arrTotalPrakKelas[] = $this->MahasiswaReport($mahasiswa)["totalPrak"];
                $arrTotalLainnyaKelas[] = $this->MahasiswaReport($mahasiswa)["totalLainnya"];
                $arrTotalTidakHadirKelas[] = $this->MahasiswaReport($mahasiswa)["tidkHadir"];
                $arrTotalKeluarKampusKelas[] = $this->MahasiswaReport($mahasiswa)["keluar"];
            }


            $totalTeori = $this->sumArrayValues($arrTotalTeoriKelas);
            $totalPrak = $this->sumArrayValues($arrTotalPrakKelas);
            $totalLainnya = $this->sumArrayValues($arrTotalLainnyaKelas);
            $tidkHadir = array_sum($arrTotalTidakHadirKelas);
            $keluar = array_sum($arrTotalKeluarKampusKelas);
        }



        $totalIjin = array($tidkHadir, $keluar);


//
//        if ($model->load(Yii::$app->request->post())) {
//            
//        } else {
        return $this->render('dwreport', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'model' => $model,
                    'kelas' => $kelas,
                    'arrMatakuliah' => $arrMatakuliah,
//                        'arrMatakuliahId' => $arrMatakuliahId,
                    'totalTeori' => $totalTeori,
                    'totalPrak' => $totalPrak,
                    'totalLainnya' => $totalLainnya,
                    'Tipe' => $Tipe,
                    'totalIzin' => $totalIjin,
                    'mahasiswaAll' => $mahasiswaAll
        ]);
//        }
    }

    public function actionDosenwali() {
        $dosen = AitkRDosen::findOne([
                    'account_id' => Yii::$app->user->id
                        ]
        );
        $kelas = AitkRKelas::findOne(['wali' => $dosen->dosen_id]);

        /* HAPUS INI UNTUK PENGGUNAAN RBAC */
        /*         * *** */
        if (!isset($kelas)) {
            throw new \yii\web\HttpException(403, 'You not authorized to enter this', 405);
        }
        if (Yii::$app->user->isGuest)
            $this->redirect(Yii::$app->homeUrl);

        /*         * *** */

        $dataProviderRejected = new ActiveDataProvider([
            'query' => AitkRequest::find()->where(['status_dosen' => 0, 'dosen_wali' => $dosen->dosen_id]),
            'pagination' => [
                'pageSize' => 3,
            ],
            'sort' => ['defaultOrder' => ['request_id' => SORT_DESC]],
        ]);

        $dataProviderPending = new ActiveDataProvider([
            'query' => AitkRequest::find()->where(['status_dosen' => NULL, 'dosen_wali' => $dosen->dosen_id]),
            'pagination' => [
                'pageSize' => 3,
            ],
            'sort' => ['defaultOrder' => ['request_id' => SORT_DESC]],
        ]);

        $dataProviderApproved = new ActiveDataProvider([
            'query' => AitkRequest::find()->where(['status_dosen' => 1, 'dosen_wali' => $dosen->dosen_id]),
            'pagination' => [
                'pageSize' => 3,
            ],
            'sort' => ['defaultOrder' => ['request_id' => SORT_DESC]],
        ]);


        $dosenId = $dosen->nama_dosen;
        $searchModel = new AitkRequestSearch();

        $searchModel->dosen_wali = $dosenId;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('dosenwali', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'dataProviderRejected' => $dataProviderRejected,
                    'dataProviderPending' => $dataProviderPending,
                    'dataProviderApproved' => $dataProviderApproved
        ]);
    }

    public function actionApprovedosen($value, $id) {
        $request = AitkRequest::findOne($id);
        $dosen = AitkRDosen::findOne(array('account_id' => Yii::$app->user->id));

        if ($request->status_dosen != NULL)
            return $this->redirect(['dosenwali']);

        $request->status_dosen = $value;
        if ($request->save()) {

            if ($value == 1) {
                $hp_asrama = $request->tujuanSmsPengurus->handphone;

                $nama = $request->mahasiswa->nama_mahasiswa;
                $nim = $request->mahasiswa->nim;
                $tempMulai = explode(' ', $request->waktu_start)[1];
                $tempSelesai = explode(' ', $request->waktu_end)[1];

                $mulai = substr($tempMulai, 0, 5);
                $selesai = substr($tempSelesai, 0, 5);


                $tipeIzin = $request->tipe_ijin == "K" ? "Keluar" : "Sakit/Tidak Hadir";
                $id = $request->request_id;
                $sms = current(explode(" ", $nama)) . "/" . $nim . "/" . $request->mahasiswa->angkatan . " " . $tipeIzin .
                        " " . $mulai .
                        "-" . $selesai . " \"" . $request->alasan_ijin . "\" " . "balas 'IZIN YA $id' atau 'IZIN TIDAK $id'";

                $this->sendSMS($sms, $hp_asrama);
            }
            return $this->redirect(['dosenwali']);
        }
    }

    public function actionAsrama() {

        $asrama = \backend\modules\aitk\models\AitkRAsrama::findOne(['account_id' => Yii::$app->user->id]);
        /* HAPUS INI UNTUK PENGGUNAAN RBAC */
        /*         * *********** */
        if (!isset($asrama)) {
            $this->redirect(Yii::$app->homeUrl);
        }
        /*         * *********** */
        $searchModel = new AitkRequestSearch();
        $dataProviderPending = new ActiveDataProvider([
            'query' => AitkRequest::find()->where(['status_asrama' => NULL, 'status_dosen' => 1]),
            'pagination' => [
                'pageSize' => 3,
            ],
            'sort' => ['defaultOrder' => ['request_id' => SORT_DESC]],
        ]);

        $dataProviderApproved = new ActiveDataProvider([
            'query' => AitkRequest::find()->where(['status_asrama' => 1, 'status_dosen' => 1]),
            'pagination' => [
                'pageSize' => 3,
            ],
            'sort' => ['defaultOrder' => ['request_id' => SORT_DESC]],
        ]);


        $dataProviderRequest = new ActiveDataProvider([
            'query' => AitkRequest::find()->where(['status_dosen' => 1])
                    ->orWhere(['status_asrama' => 0]),
            'pagination' => [
                'pageSize' => 3,
            ],
            'sort' => ['defaultOrder' => ['request_id' => SORT_DESC]],
        ]);
        $dataProviderRejected = new ActiveDataProvider([
            'query' => AitkRequest::find()->where(['status_asrama' => 0, 'status_dosen' => 1]),
            'pagination' => [
                'pageSize' => 3,
            ],
            'sort' => ['defaultOrder' => ['request_id' => SORT_DESC]],
        ]);


        return $this->render('asrama', [
                    'searchModel' => $searchModel,
                    'dataProviderPending' => $dataProviderPending,
                    'dataProviderApproved' => $dataProviderApproved,
                    'dataProviderRejected' => $dataProviderRejected,
                    'dataProviderRequest' => $dataProviderRequest,
        ]);
    }

    public function actionApproveasrama($value, $id) {
        $asrama = AitkRAsrama::findOne(['account_id' => Yii::$app->user->id]);
        if (!isset($asrama)) {
            $this->redirect(Yii::$app->homeUrl);
        }
        $model = AitkRequest::findOne($id);
        if ($model->status_asrama != NULL)
            return $this->redirect(['asrama']);
        $model->status_asrama = $value;
        $model->pengurus_asrama = $asrama->asrama_id;

        if ($model->save()) {
            if (strtolower($model->tipe_ijin) == "tidak hadir")
                $this->InsertMatakuliahizin($model->request_id);

            return $this->redirect(['asrama']);
        }
    }

    public function actionSendmail($id) {

        $request = AitkRequest::findOne($id);

        $date = date('H:i');
        $sapa = '';
        if ($date < 12)
            $sapa = "Pagi";
        else if ($date < 14)
            $sapa = 'Siang';
        else if ($date < 18)
            $sapa = 'Sore';


        $mhs = AitkRMahasiswa::findOne(['mahasiswa_id' => $request->mahasiswa_id]);
        $asrama = AitkRAsrama::findOne(['account_id' => Yii::$app->user->id]);


        $message = 'Yth. Bapak/Ibu Dosen/Staff <br>'
                . 'Selamat ' . $sapa . ' Bapak/Ibu Sekalian. Kami memberitahukan bahwa salah seorang mahasiswa tidak dapat hadir '
                . ' sbb:   <br>'
                . ' Nama: ' . $mhs->nama_mahasiswa . ' <br>'
                . ' Nim : ' . $mhs->nim . ' <br>'
                . ' Kelas : ' . $mhs->kelas->kode_kelas
                . ' <br> <br>Atas perhatian Bapak/Ibu Sekalian, Kami Ucapkan Terimakasih'
                . '<br><br><br> <hr> Dikirim oleh : <br>'
                . '<b>Aptikad (Aplikasi Izin Tidak Hadir Jam Akademik)<b>';


        $model = new FormSendEmail();
        $model->message = $message;
        if ($model->load(Yii::$app->request->post())) {

            $tujuan = 'if313032@students.del.ac.id';

            $this->Sendmail($message, $tujuan);

            return $this->redirect(['asrama']);
        } else {
            return $this->renderAjax('editEmail', [
                        'model' => $model
            ]);
        }
    }

    public function Sendmail($message, $tujuan) {
        $email = Yii::$app->mailer->compose()
                ->setFrom([\Yii::$app->params['supportEmail'] => 'APTIKAD'])
                ->setTo($tujuan)
                ->setSubject("[IZIN] Tidak Hadir Pada Jam Kuliah")
                ->setHtmlBody($message)
                ->send();

        return $email;
    }

    public function actionAlasanreject($id) {
        $model = new FormALasanReject();
        if ($model->load(Yii::$app->request->post())) {
            $request = AitkRequest::findOne($id);
            $request->alasan_penolakan = $model->alasan_penolakan;

            if (!isset($request->status_dosen)) {
                $request->status_dosen = 0;
                $request->status_asrama = 0;

                if ($request->save())
                    $this->redirect(['dosenwali']);
            }
            else if (!isset($request->status_asrama)) {
                $request->status_asrama = 0;
                $request->status_dosen = 0;
                if ($request->save())
                    $this->redirect(['asrama']);
            }
        } else {
            return $this->renderAjax('alasanTolak', [
                        'model' => $model
            ]);
        }
    }

    public function actionCancelrequest($id) {
        $model = AitkRequest::findOne($id);


        if ($model->delete())
            return $this->redirect(['index']);
    }

    /**
     * Updates an existing AitkRequest model.
     * If update is successful, the browser will be redirected to the 'detail' page.
     * @param integer $id
     * @return mixed
     */
    public function actionEdit($id) {

        $model = $this->findModel($id);
        $formIzin = new FormIzin();

        if ($model->load(Yii::$app->request->post())) {
            return $this->redirect(['index', 'id' => $model->request_id]);
        } else {


            $asrama = AitkRMahasiswa::findOne(['account_id' => Yii::$app->user->id]);
            $level = 0;

            if (isset($asrama)) {
                $level = 1;
            }

            return $this->render('edit', [
                        'model' => $model,
                        'level' => $level
            ]);
        }
    }

    /**
     * Finds the AitkRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AitkRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = AitkRequest::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function sendSMS($sms, $number) {

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
//            echo "Succes Sent";
        } else {
//            echo "Halo";
            print_r($kirimsms->getErrors());
        }


//        die();
    }

    public function actionPrintpreview($id) {
        $model = AitkRequest::findOne($id);
        return $this->renderAjax('printPreview', [
                    'model' => $model
        ]);
    }

    public function actionPrint($id) {

        $model = AitkRequest::findOne($id);

        $mpdf = new mPDF('utf-8', 'A4-L');


        
        
        $mpdf->SetColumns(2);

        $mpdf->WriteHTML(
                $this->renderPartial('_printPreview', [
                    'model' => $model
        ]));
        $mpdf->AddColumn();

        $mpdf->WriteHTML(
                $this->renderPartial('_printPreview', [
                    'model' => $model
        ]));
        $mpdf->Output('form.pdf', 'D');
        $mpdf->Output();
        exit();
    }

    public function MahasiswaReport($mhs) {

        $matakuliah = AitkRMatakuliah::find()->where(['semester' => $mhs->semester])->andWhere(['jurusan' => NULL])->orWhere(['jurusan' => $mhs->jurusan])->all();
        $tidkHadir = (int) AitkRequest::find()->where(["tipe_ijin" => "Tidak Hadir", 'status_dosen' => 1, "status_asrama" => 1, "mahasiswa_id" => $mhs->mahasiswa_id])->count();
        $keluar = (int) AitkRequest::find()->where(["tipe_ijin" => "Keluar", 'status_dosen' => 1, "status_asrama" => 1, "mahasiswa_id" => $mhs->mahasiswa_id])->count();

        $tidakHadir = AitkRequest::find()->where(["tipe_ijin" => "Tidak Hadir", 'status_dosen' => 1, "status_asrama" => 1, "mahasiswa_id" => $mhs->mahasiswa_id])->asArray()->all();

        $arrIzin = array();
        foreach ($tidakHadir as $arrayTidakHadir) {
            $arrIzin [] = AitkRequest::find()->where(["aitk_request.request_id" => $arrayTidakHadir['request_id'], 'aitk_request.status_dosen' => 1, "aitk_request.status_asrama" => 1])->joinWith('aitkMatakuliahizins', true, "INNER JOIN")->asArray()->all();
        }

        $matakuliahIzinMhs = array();

        foreach ($arrIzin as $izins) {
            foreach ($izins as $izin) {
                foreach ($izin['aitkMatakuliahizins'] as $sesiMatakuliah) {

                    $matakuliahIzinMhs [] = $sesiMatakuliah;
                }
            }
        }

        $teoriId = array();
        $prakId = array();
        $lainnya = array();
        foreach ($matakuliahIzinMhs as $mta) {
            if ($mta ['sesi'] == "T") {
                $teoriId[] = $mta["matakuliah_id"];
            }
            if ($mta['sesi'] == "P")
                $prakId[] = $mta["matakuliah_id"];
            if ($mta['sesi'] == NULL)
                $lainnya[] = $mta["matakuliah_id"];
        }

        $teoriId = array_count_values($teoriId);
        $prakId = array_count_values($prakId);
        $lainnya = array_count_values($lainnya);

        $totalTeori = array();
        $totalPrak = array();
        $totalLainnya = array();
        $arrMatakuliah = array();
        foreach ($matakuliah as $matkul) {
            $arrMatakuliah [] = $matkul['alias'];

            if (array_key_exists($matkul["matakuliah_id"], $teoriId)) {
                $totalTeori [] = $teoriId[$matkul["matakuliah_id"]];
            } else {
                $totalTeori [] = 0;
            }


            if (array_key_exists($matkul["matakuliah_id"], $prakId)) {
                $totalPrak [] = $prakId[$matkul["matakuliah_id"]];
            } else {
                $totalPrak [] = 0;
            }

            if (array_key_exists($matkul["matakuliah_id"], $lainnya)) {
                $totalLainnya [] = $lainnya[$matkul["matakuliah_id"]];
            } else {
                $totalLainnya [] = 0;
            }
        }

        $arr = array();

        $arr["totalTeori"] = $totalTeori;
        $arr["totalPrak"] = $totalPrak;
        $arr["totalLainnya"] = $totalLainnya;
        $arr["tidkHadir"] = $tidkHadir;
        $arr["keluar"] = $keluar;
        $arr["arrMatakuliah"] = $arrMatakuliah;


        return $arr;
    }

    public function sumArrayValues($array) {
        $arrReturn = array();
        foreach ($array as $arr => $key) {
            foreach ($key as $val => $nilai) {
                if (array_key_exists($val, $arrReturn)) {
                    $arrReturn[$val]+=$nilai;
                } else {
                    $arrReturn[$val] = $nilai;
                }
            }
        }
        return $arrReturn;
    }

}
