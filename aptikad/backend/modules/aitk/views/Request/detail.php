<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model backend\modules\aitk\models\AitkRequest */

//$this->title = $model->request_id;
//$this->params['breadcrumbs'][] = ['label' => 'Aitk Requests', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="aitk-request-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <div id="akord">

        <div class="panel-info">

            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#akord" href="#collapseRequest">Detail Request</a>
                </h4>
            </div>

            <div id="collapseRequest" class="panel-collapse collapse <?php if (!isset($filled)) echo 'in'; ?>">
                <div class="panel-body">

                    <div class="row">

                        <div class="col-lg-12">
                            <?=
                            DetailView::widget([
                                'model' => $model,
                                'attributes' => [
//            'request_id',         
                                    [
                                        'attribute' => 'dosenWali.nama_dosen',
                                        'label' => 'Dosen Wali',
                                    ],
                                    [
//             
                                        'label' => 'Requester',
                                        'format' => 'raw',
                                        'value' => $model->requester === NULL ? "<span class='label label-info'>" . $model->pengurusAsrama->nama_pengurus . "</span>" : "<span class='label label-warning'>" . $model->requester0->nama_mahasiswa . "</span>",
                                    ],
                                    [
                                        'attribute' => 'mahasiswa.nama_mahasiswa',
                                        'label' => 'Mahasiswa',
                                    ],
                                    'tipe_ijin',
                                    [
                                        'attribute' => 'waktu_start',
                                        'label' => 'Mulai',
                                    ],
                                    [
                                        'attribute' => 'waktu_end',
                                        'label' => 'Selesai',
                                    ],
                                    'alasan_ijin:ntext',
                                    'lampiran',
                                    [
                                        'attribute' => 'file_lampiran',
                                        'value' => empty($model->file_lampiran)?"":'@web/file_lampiran/'.$model->file_lampiran,
                                        'format' => empty($model->file_lampiran)?"raw":['image', ['width' => '80%', 'height' => '80%']],
                                    ],
                                    [
                                        'attribute' => 'status_dosen',
                                        'format' => 'raw',
                                        'value' => $model->status_dosen === 1 ? "<span class='label label-success'>Approved</span>" : ($model->status_dosen === 0 ? "<span class='label label-danger'> Rejected </span>" : "<span class='label label-warning'>Pending </span>")
                                    ],
                                    [
                                        'attribute' => 'status_asrama',
                                        'format' => 'raw',
                                        'value' => $model->status_asrama === 1 ? "<span class='label label-success'>Approved</span>" : ($model->status_asrama === 0 ? "<span class='label label-danger'> Rejected </span>" : "<span class='label label-warning'>Pending</span>")
                                    ],
                                ],
                            ])
                            ?>
                        </div></div>
                </div></div>
        </div>
        <?php if (isset($filled)) : ?>

            <div class="panel-warning">

                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#akord" href="#collapseIzin">Matakuliah Izin</a>
                    </h4>
                </div>

                <div id="collapseIzin" class="panel-collapse collapse">
                    <div class="panel-body">

                        <div class="row">

                            <div class="col-lg-12">
                                <?php
//                                print_r($matkulIzin);

                                Pjax::begin();
                                echo GridView::widget([
                                    'dataProvider' => $matkulIzin,
                                    'showOnEmpty' => false,
                                    'columns' => [
                                        [
                                            'attribute' => 'matakuliah_id',
                                            'value' => 'matakuliahizin.matakuliah',
                                            'label' => 'Matakuliah'
                                        ],
//                                        [
//                                            'attribute' => 'dosen_id',
//                                            'value' => 'dosen.nama_dosen',
//                                            'label' => 'Dosen'
//                                        ],
                                        [
                                            'attribute' => 'sesi',
                                            'format' => 'raw',
                                            'value' => function ($data) {
                                                return ($data->sesi === 'T' ? '<span class="label label-success">Teori</span>' : '<span class="label label-info">Praktikum</span>');
                                            },
                                        ],
//                                         [
//                                             
//                                         ],
                                ]]);
                                Pjax::end();
                                ?>
                            </div></div>
                    </div></div>
            </div>

        <?php endif; ?>

    </div>
</div>
