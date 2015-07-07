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
use yii\kartik\ActiveForm;
use kartik\field;
use kartik\builder\Form;
use backend\modules\aitk\models\AitkRMatakuliah;
use backend\modules\aitk\models\AitkRKelas;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\aitk\models\search\AitkRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */



$this->title = 'Summary ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="aitk-request-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php
    if (isset($mahasiswa)):
        ?>


        <div class="panel panel-info" id="dataMahasiswa" >
            <!-- Default panel contents -->
            <div class="panel-heading">Data Mahasiswa</div>

            <!-- Table -->
            <table class="table">

                <tr><td>Nama</td> <td>:</td> <td><p id="namaMahasiswa"><?= $mahasiswa->nama_mahasiswa ?></p></td></tr>

                <tr><td>NIM</td> <td>:</td> <td><p id="nimMahasiswa"><?= $mahasiswa->nim ?></p></td></tr>

                <tr><td>Kelas</td> <td>:</td> <td><p id="kelasMahasiswa"><?= $mahasiswa->kelas->kode_kelas ?></p></td></tr>

                <tr><td>Semester</td> <td>:</td> <td><p id="semesterMahasiswa"><?= $mahasiswa->semester ?></p></td></tr>

                <tr><td>Wali</td> <td>:</td> <td><p id="waliMahasiswa"><?= $mahasiswa->kelas->wali0->nama_dosen ?></p></td></tr>

            </table>
            <?= Html::a('Back To Summary', ['baak'], ['class' => 'btn btn-info']) ?>

        </div>


        <?php
    endif;
    ?>

    <?php
    if (isset($kelas)):
        $totalMhs = \backend\modules\aitk\models\AitkRMahasiswa::find()->where(['kelas_id' => $kelas->kelas_id])->count();
        ?>

        <?= Html::a('Back To All Request', ['dosenwali'], ['class' => 'btn btn-info']) ?>

        <div class="panel panel-info" id="dataKelas" >
            <!-- Default panel contents -->
            <div class="panel-heading">Data Kelas</div>

            <!-- Table -->
            <table class="table">

                <tr><td>Kelas</td> <td>:</td> <td><p id="namaKelas"><?= $kelas->nama_kelas ?></p></td></tr>

                <tr><td>Alias</td> <td>:</td> <td><p id="aliasKelas"><?= $kelas->kode_kelas ?></p></td></tr>

                <tr><td>Wali Kelas</td> <td>:</td> <td><p id="dosenWali"><?= $kelas->wali0->nama_dosen ?></p></td></tr>

                <tr><td>Jumlah Mahasiswa</td> <td>:</td> <td><p id="totalMahasiswa"><?= $totalMhs ?></p></td></tr>
            </table>



        </div>


        <?php
    endif;
    ?>

    <?php
    if (!empty($kelas) || !empty($mahasiswa)) :
        ?>


        <div class="row">

            <?=
            HighCharts::widget([
                'clientOptions' => [
                    'chart' => [
                        'type' => 'column'
                    ],
                    'title' => [
                        'text' => 'Total Izin'
                    ],
                    'xAxis' => [
                        'categories' => array("Total Izin")
                    ],
                    'yAxis' => [
                        'title' => [
                            'text' => 'Total Izin'
                        ]
                    ],
                    'series' => [
                        ['name' => 'Tidak Hadir', 'data' => array($totalIzin[0])],
                        ['name' => 'Keluar Kampus', 'data' => array($totalIzin[1])],
                    ]
                ],
            ]);
            ?>


            <?=
            HighCharts::widget([
                'clientOptions' => [
                    'chart' => [
                        'type' => 'column'
                    ],
                    'title' => [
                        'text' => 'Total Izin Matakuliah'
                    ],
                    'xAxis' => [
                        'categories' => $arrMatakuliah
                    ],
                    'yAxis' => [
                        'title' => [
                            'text' => 'Total Izin'
                        ]
                    ],
                    'series' => [
                        ['name' => 'Teori', 'data' => $totalTeori],
                        ['name' => 'Praktikum', 'data' => $totalPrak],
                        ['name' => 'Lainnya', 'data' => $totalLainnya],
                    ]
                ],
            ]);
            ?>
            <?php
        endif;
        ?>


    </div>

    <div class="row">
        <?php if (isset($kelas)) :
            ?>

            <div class="panel-group" id="accordion">


                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseList"> <h4>List Mahasiswa</h4></a>
                        </h4>
                    </div>
                    <div id="collapseList" class="panel-collapse collapse ">
                        <table class="table" >
                            <tr><th>No.</th> <th>Nim</th> <th>Nama</th> <th>Total Izin </th> <th>View Report</th></tr>
                            <?php
                            $i = 1;
                            $total = 0;
                            foreach ($mahasiswaAll as $mhs) {

                                $count = AitkRequest::find()->where(['mahasiswa_id' => $mhs->mahasiswa_id, 'status_dosen' => 1, 'status_asrama' => 1])->count();
                                $label = $count > 0 ? "<span class='label label-success'>" . $count . "</span>" : "<span class='label label-warning'> 0</span>";
                                echo "<tr> <td>" . $i . "</td>" . "<td>" . $mhs->nim .
                                "</td><td>" . $mhs->nama_mahasiswa . "</td><td>" . $label . "</td> <td>" .
                                Html::a('View Report', ['reportdw', 'nim' => $mhs->nim], ['class' => 'btn btn-info']) .
                                " </td></tr>";

                                $i++;
                                $total+=$count;
                            }
                            ?>
                            <tr>
                                <td colspan="3" style="text-align: center"><h4>Total</h4></td><td><?= "<span class= 'label label-danger'>" . $total . "</span>" ?></td><td></td>
                            </tr>         

                        </table>

                    </div>
                </div>
            </div>


        <?php endif; ?>
    </div>
    <div class="row">

        <h3>All Request Log</h3>
        <?php
        Pjax::begin();
        $this->registerJs('
    $(".detailView").click(function() {
        $("#modalDetail").modal("show").
        find("#modalDetailContent").
        load($(this).attr("value"))
        });

        ', \yii\web\View::POS_READY);

        Modal::begin([
            'header' => 'Detail Request',
            'id' => 'modalDetail',
            'size' => 'modal-md'
        ]);

        echo "<div id= 'modalDetailContent'></div>";

        Modal::end();

        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'dosen_wali',
                    'value' => 'dosenWali.nama_dosen'
                ],
                [
                    'attribute' => 'requester',
                    'label' => 'Requester',
                    'format' => 'raw',
                    'value' => function ($data) {
                        return $data->requester === NULL ? "<span class='label label-info'>" . $data->pengurusAsrama->nama_pengurus . "</span>" : "<span class='label label-warning'>" . $data->requester0->nama_mahasiswa . "</span>";
                    }
                ],
                'tipe_ijin',
                'waktu_start',
                'waktu_end',
                'alasan_ijin',
                [
                    'attribute' => 'status_dosen',
                    'format' => 'raw',
                    'value' => function ($data) {
                        //  return $data->status_dosen == 1 ? "Approved" : ($data->status_dosen == NULL ? "Pending" : "Rejected");
                        return $data->status_dosen == 1 ? "<span class='label label-success'>Approved</span>" : ($data->status_dosen === 0 ? "<span class='label label-danger'> Rejected </span>" : "<span class='label label-warning'>Pending </span>");
                    }
                ],
                [
                    'attribute' => 'status_asrama',
                    'format' => 'raw',
                    'value' => function ($data) {
                        return $data->status_asrama == 1 ? "<span class='label label-success'>Approved</span>" : ($data->status_asrama === 0 ? "<span class='label label-danger'> Rejected </span>" : "<span class='label label-warning'>Pending</span>");
                    }
                ],
//                                    [
//                                        'attribute' => 'pengurus_asrama',
//                                        'value' => 'pengurusAsrama.nama_pengurus',
//                                        'label' =>'Approved By' 
//                                    ],        
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [

                        'view' => function ($url, $model) {

                            return Html::a(
                                            '<span class="glyphicon glyphicon-eye-open"></span>', "#", [
                                        'title' => Yii::t('yii', 'View Detail'),
                                        'class' => 'detailView',
                                        'value' => Url::to('index.php?r=aitk/request/detail&id=' . $model->request_id),
                            ]);
                        },],
                        ]
                    ],
                ]);
                Pjax::end();
                ?>


    </div>
</div>

