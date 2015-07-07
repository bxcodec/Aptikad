<?php

use yii\helpers\Html;
use dosamigos\highcharts\HighCharts;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use backend\modules\aitk\models\AitkRequest;
use yii\data\ActiveDataProvider;
use backend\modules\aitk\models\search\AitkRequestSearch;
use yii\web\UrlManager;
use yii\helpers\Url;
use backend\modules\aitk\models\AitkRDosen;
use backend\modules\aitk\models\AitkMatakuliahizin;
use backend\modules\aitk\models\AitkDosenmatakuliah;
use backend\modules\aitk\models\AitkRMatakuliah;
use backend\modules\aitk\models\AitkRKelas;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\aitk\models\search\AitkRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */



$this->title = 'Summary';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="aitk-request-index">

    <h1><?= Html::encode($this->title) ?></h1>



    <?php
    $dosen = AitkRDosen::find()->where(['account_id' => Yii::$app->user->id])->one();

    $kelas = AitkRKelas::findOne(['wali' => $dosen->dosen_id]);


    if (isset($kelas)) {
        $iddosenWali = $dosen->dosen_id;
        ?><p>
            <?= Html::a('Go to Dosen Wali Section', ['dosenwali'], ['class' => 'btn btn-info']) ?>
        </p>
    <?php } ?>


    <div class="body-content">

        <?php
        $matakuliah = AitkDosenmatakuliah::find()->where(['dosen_id' => $dosen->dosen_id])->all();
        $arrMatakuliahId = array();
        $arrMatakuliah = array();
        $arrTotalJam = array();
        $arrayObjMatkul = array();
        foreach ($matakuliah as $matkul) {
            $arrMatakuliahId[] = $matkul['matakuliah_id'];
            $arrayObjMatkul[] = AitkRMatakuliah::findOne($matkul['matakuliah_id']);
        }


        $totalTeori = array();
        $totalPrak = array();
        $totalLainnya = array();

        foreach ($arrayObjMatkul as $ObjMatakuliah) {
            $arrMatakuliah[] = $ObjMatakuliah['alias'];
            $arrTotalJam[] = $ObjMatakuliah['jumlah_jam'];
            $totalTeori[] = (int) AitkMatakuliahizin::find()->where(['matakuliah_id' => $ObjMatakuliah['matakuliah_id'], 'sesi' => 'T'])->count();
            $totalPrak[] = (int) AitkMatakuliahizin::find()->where(['matakuliah_id' => $ObjMatakuliah['matakuliah_id'], 'sesi' => 'P'])->count();
            $totalLainnya [] = (int) AitkMatakuliahizin::find()->where(['matakuliah_id' => $ObjMatakuliah['matakuliah_id'], 'sesi' => NULL])->count();
        }
        ?>

        <div class="row">

            <?=
            HighCharts::widget([
                'clientOptions' => [
                    'chart' => [
                        'type' => 'column'
                    ],
                    'title' => [
                        'text' => 'Total Ketidakhadiran Matakuliah/ Semester'
                    ],
                    'xAxis' => [
                        'categories' => $arrMatakuliah
                    ],
                    'yAxis' => [
                        'title' => [
                            'text' => 'Jumlah'
                        ]
                    ],
                    'series' => [
                        ['name' => 'Total Izin Jam Teori', 'data' => $totalTeori],
                        ['name' => 'Total Izin Jam Praktikum', 'data' => $totalPrak],
                        ['name' => 'Total IzinKeseluruhan', 'data' => $totalLainnya],
                    ]
                ],
            ]);
        
            ?>

        </div>
        <div class="row">
            <div class="panel-group" id="accordion">

                <?php
                $i = 1;
                foreach ($arrayObjMatkul as $ObjMatakuliah) :
                    ?>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $ObjMatakuliah->kode_matakuliah ?>"> <?php echo $i . ". " . $ObjMatakuliah->matakuliah ?></a>
                            </h4>
                        </div>
                        <div id="collapse<?php echo $ObjMatakuliah->kode_matakuliah; ?>" class="panel-collapse collapse<?php if ($i === 1) echo " in"; ?>">
                            <div class="panel-body">

                                <div class="col-lg-12">
                                    <?php
                                    $izinMatkul = AitkMatakuliahizin::find()->where(['matakuliah_id' => $ObjMatakuliah->matakuliah_id])->all();
                                    $izinMatkulCount = AitkMatakuliahizin::find()->where([
                                                'matakuliah_id' => $ObjMatakuliah->matakuliah_id,
                                            ])->count();
                                    $prak = AitkMatakuliahizin::find()->where([
                                                'matakuliah_id' => $ObjMatakuliah->matakuliah_id,
                                                'sesi' => 'P'
                                            ])->count();
                                    $teori = AitkMatakuliahizin::find()->where([
                                                'matakuliah_id' => $ObjMatakuliah->matakuliah_id,
                                                'sesi' => 'T'
                                            ])->count();
                                    ?>

                                    <p>
                                        Total Izin Tidak Hadir : <?php echo $izinMatkulCount; ?> 
                                        <br>                                </p>

                                    <ul>
                                        <li>
                                            <span class="label label-info"><?php echo $teori ?></span> Pada sesi teori
                                        </li>
                                        <li>
                                            <span class="label label-primary"><?php echo $prak ?></span> Pada sesi Praktikum
                                        </li>

                                        <li>
                                            <span class="label label-warning"><?php echo $izinMatkulCount - ($teori + $prak) ?></span> Lainnya
                                        </li>


                                    </ul>
                                    <span class=" label label-success ">
                                        * Lainnya : Data yang dengan pengisian form tidak lengkap oleh mahasiswa.
                                    </span>

                                </div>

                                <div class="col-lg-12">
                                    <div class="table-responsive">

                                        <?php
                                        Pjax::begin();

                                        $dataProvider = new ActiveDataProvider([
                                            'query' => AitkMatakuliahizin::find()->where([
                                                'matakuliah_id' => $ObjMatakuliah->matakuliah_id
                                            ]),
                                            'pagination' => [
                                                'pageSize' => 3,
                                            ],
                                            'sort' => ['defaultOrder' => ['request_id' => SORT_DESC]],
                                        ]);

                                        echo GridView::widget([
                                            'dataProvider' => $dataProvider,
                                            'showOnEmpty' => false,
                                            'columns' => [
                                                ['class' => 'yii\grid\SerialColumn'],
                                                [
                                                    'attribute' => 'request_id',
                                                    'value' => 'request.waktu_start',
                                                    'label' => 'Tanggal Request'
                                                ],
                                                [
                                                    'attribute' => 'sesi',
                                                    'label' => 'Sesi',
                                                    'format' => 'raw',
                                                    'value' => function ($data) {
                                                        return $data->sesi === 'T' ? "<span class='label label-info'>Teori</span>" : "<span class='label label-warning'>Praktikum</span>";
                                                    }
                                                ],
                                                                                                       
                                                [
                                                    'attribute' => 'request_id',
                                                    'value' => 'request.mahasiswa.nama_mahasiswa',
                                                    'label' => 'Mahasiswa'
                                                ],
                                                [
                                                    'attribute' => 'request_id',
                                                    'value' => 'request.mahasiswa.kelas.kode_kelas',
                                                    'label' => 'Kelas'
                                                ],
                                                
                                            ],
                                        ]);

                                        Pjax::end();
                                        ?>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $i++;
                endforeach;
                ?>
            </div>

        </div>
    </div>
</div>
