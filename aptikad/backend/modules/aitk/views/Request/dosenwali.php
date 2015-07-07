<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use backend\modules\aitk\models\AitkRequest;
use yii\data\ActiveDataProvider;
use backend\modules\aitk\models\search\AitkRequestSearch;
use yii\web\UrlManager;
use yii\helpers\Url;
use backend\modules\aitk\models\AitkRDosen;

$this->title = 'List Of Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="aitk-request-index">

    <h1><?= Html::encode($this->title) ?></h1>


    
    <?= Html::a('Go to Dosen Section', ['dosen'], ['class' => 'btn btn-info']) ?><br> <br>
    <?= Html::a('View Summary', ['reportdw'], ['class' => 'btn btn-info']) ?>
    <?php
    Modal::begin([
        'header' => 'Alasan Penolakan',
        'id' => 'modalTolak',
        'size' => 'modal-md'
    ]);

    echo "<div id= 'modalContent'></div>";

    Modal::end();


    Modal::begin([
        'header' => 'Detail',
        'id' => 'modalDetail',
        'size' => 'modal-md'
    ]);

    echo "<div id= 'modalDetailContent'></div>";

    Modal::end();
    ?>

    <br>
    <br>
    <br>
    <div class="table-responsive">
        <div class="panel-group" id="accordion">

            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapsePending"> Pending Request</a>
                    </h4>
                </div>
                <div id="collapsePending" class="panel-collapse collapse in">
                    <div class="panel-body">

                        <div class="row">

                            <div class="col-lg-12">

                                <?php
                                Pjax::begin();
                                $this->registerJs('
    $(".rejectWali").click(function() {
        $("#modalTolak").modal("show").
        find("#modalContent").
        load($(this).attr("value"))
        });

        ', \yii\web\View::POS_READY);
                                $this->registerJs('
    $(".detailView").click(function() {
        $("#modalDetail").modal("show").
        find("#modalDetailContent").
        load($(this).attr("value"))
        });

        ', \yii\web\View::POS_READY);



                                echo GridView::widget([
                                    'dataProvider' => $dataProviderPending,
                                    'showOnEmpty' => false,
                                    'columns' => [
                                        ['class' => 'yii\grid\SerialColumn'],
                                        
                                        
                                        [
                                            'attribute' => 'mahasiswa_id',
                                            'value' => 'mahasiswa.nama_mahasiswa',
                                            'label' => 'Ijin Untuk'
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
                                        'alasan_ijin',
                                        [
                                            'attribute' => 'status_dosen',
                                            'label' => 'Status Request',
                                            'format' => 'raw',
                                            'value' => function ($data) {
                                                return $data->status_dosen === 1 ? "<span class='label label-success'>Approved</span>" : ($data->status_dosen === 0 ? "<span class='label label-danger'> Rejected </span>" : "<span class='label label-warning'>Pending</span>");
                                            }
                                        ],
                                        [

                                            'class' => 'yii\grid\ActionColumn',
                                            'template' => '{view} {approvedosen} {rejectdosen}',
                                            'buttons' => [

                                                'view' => function ($url, $model) {

                                                    return Html::a(
                                                                    '<span class="glyphicon glyphicon-eye-open"></span>', "#", [
                                                                'title' => Yii::t('yii', 'View Detail'),
                                                                'class' => 'detailView',
                                                                'value' => Url::to('index.php?r=aitk/request/detail&id=' . $model->request_id),
                                                    ]);
                                                },
                                                        'approvedosen' => function ($url, $model) {

                                                    return Html::a(
                                                                    '<span class="glyphicon glyphicon-ok"></span>', $url, [
                                                                'title' => Yii::t('yii', 'Approve Request'),
//                                                    'data-confirm' => Yii::t('yii', 'Download Form ?'),
                                                                'data-method' => 'post',
                                                    ]);
                                                },
                                                        'rejectdosen' => function ($url, $model) {

                                                    return Html::a(
                                                                    '<span class="glyphicon glyphicon-remove"></span>', "#", [
                                                                'title' => Yii::t('yii', 'Reject Request'),
//                                                    'data-confirm' => Yii::t('yii', 'Download Form ?'),
//                                                        'data-method' => 'post',
                                                                'class' => 'rejectWali',
                                                                'value' => Url::to('index.php?r=aitk/request/alasanreject&id=' . $model->request_id),
//                                                  
                                                    ]);
                                                },
                                                    ],
                                                    'urlCreator' => function ($action, $model, $key, $index) {
                                                if ($action === 'approvedosen') {
                                                    $url = Url::toRoute(['approvedosen', 'value' => 1, 'id' => $model->request_id]); // your own url generation logic
                                                    return $url;
                                                }
                                            }
                                                ],
                                            ],
                                        ]);
                                        Pjax::end();
                                        ?>

                                        <!--Pending Request Selesai-->

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

  <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseRequest">All Request</a>
                        </h4>
                    </div>
                    <div id="collapseRequest" class="panel-collapse collapse ">
                        <div class="panel-body">


                            <div class="row">

                                <div class="col-lg-12">
                                    <?php
                                    Pjax::begin();
                                    $this->registerJs('
                                            $(".detailView").click(function() {
                                                $("#modalDetail").modal("show").
                                                find("#modalDetailContent").
                                                load($(this).attr("value"))
                                                });

                                                ', \yii\web\View::POS_READY);
                                    ?>
                                    <?=
                                    GridView::widget([
                                        'dataProvider' => $dataProvider,
                                        'filterModel' => $searchModel,
                                        //'pagination' =>
                                        'columns' => [
                                            ['class' => 'yii\grid\SerialColumn'],
                                            // 'request_id' => 'mahasiswa.nama_mahasiswa',
                                            [
                                                'attribute' => 'requester',
                                                'label' => 'Requester',
                                                'format' => 'raw',
                                                'value' => function ($data) {
                                                    return $data->requester === NULL ? "<span class='label label-info'>" . $data->pengurusAsrama->nama_pengurus . "</span>" : "<span class='label label-warning'>" . $data->requester0->nama_mahasiswa . "</span>";
                                                }
                                            ],
                                            [
                                                'attribute' => 'mahasiswa_id',
                                                'value' => 'mahasiswa.nama_mahasiswa',
                                                'label' => 'Mahasiswa'
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
                                            'alasan_ijin',
                                            [
                                                'attribute' => 'status_dosen',
                                                'label' => 'Status Request',
                                                'format' => 'raw',
                                                'value' => function ($data) {
                                                    return $data->status_dosen === 1 ? "<span class='label label-success'>Approved</span>" : ($data->status_dosen === 0 ? "<span class='label label-danger'> Rejected </span>" : "<span class='label label-warning'>Pending</span>");
                                                }
                                            ],
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
                                                    },
                                                        ],
                                                    ],
                                                ],
                                            ]);
                                            ?>

                                            <?php Pjax::end() ?>

                        </div>
                    </div>

                </div>
            </div>
        </div>

                </div>
              

    </div>

</div>
