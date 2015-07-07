<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use backend\modules\aitk\models\AitkRequest;
use backend\modules\aitk\models\search\AitkRequestSearch;
use yii\web\UrlManager;
use yii\helpers\Url;
use backend\modules\aitk\models\AitkRMahasiswa;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\aitk\models\search\AitkRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dataProviderPending yii\data\ActiveDataProvider */
/* @var $dataProviderRejected yii\data\ActiveDataProvider */

$this->title = 'List Of Request';
$this->params['breadcrumbs'][] = $this->title;
?>
<div>

    <?php
    if (Yii::$app->getSession()->hasFlash('success')) {

        echo '<div class=flass-success>' . Yii::$app->getSession()->getFlash('success') . '</div>';
    }
    ?>


</div>


<div class="laitk-request-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?php //  Html::button('Request Izin', ['value' => Url::to('index.php?r=aitk/request/add'), 'class' => 'btn btn-info', 'id' => 'modalButton']) ?>
        <?= Html::a('Request Izin', ['requestizin'], ['class' => 'btn btn-info']) ?>

    </p>
    
    
    
    
    <div class="table-responsive">

        <?php
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

        Modal::begin([
            'header' => 'Detail Request',
            'id' => 'modalPrintPreview',
            'size' => 'modal-lg'
        ]);

        echo "<div id= 'modalPrint'></div>";

        Modal::end();
        ?>



        <div class="panel-group" id="accordion">


            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapsePending">Pending Request</a>
                    </h4>
                </div>
                <div id="collapsePending" class="panel-collapse collapse in">
                    <div class="panel-body">

                        <div class="col-lg-12">
                            <?php
                            Pjax::begin();
                            echo GridView::widget([
                                'dataProvider' => $dataProviderPending,
                                'showOnEmpty' => false,
                                'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],
                                    [
                                        'attribute' => 'dosen_wali',
                                        'value' => 'dosenWali.nama_dosen'
                                    ],
                                    'attribute' => 'tipe_ijin',
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
                                        'format' => 'raw',
                                        'value' => function ($data) {
                                     
                                            return $data->status_dosen === 1 ? "<span class='label label-success'>Approved</span>" : ($data->status_dosen === 0 ? "<span class='label label-danger'> Rejected </span>" : "<span class='label label-warning'>Pending </span>");
                                        }
                                    ],
                                    [
                                        'attribute' => 'status_asrama',
                                        'format' => 'raw',
                                        'value' => function ($data) {
                                            return $data->status_asrama === 1 ? "<span class='label label-success'>Approved</span>" : ($data->status_asrama === 0 ? "<span class='label label-danger'> Rejected </span>" : "<span class='label label-warning'>Pending</span>");
                                        }
                                    ],
                                    [

                                        'class' => 'yii\grid\ActionColumn',
                                        'template' => '{view}  {cancel} ',
                                        'buttons' => [

                                            'view' => function ($url, $model) {

                                                return Html::a(
                                                                '<span class="glyphicon glyphicon-eye-open"></span>', "#", [
                                                            'title' => Yii::t('yii', 'View Detail'),
                                                            'class' => 'detailView',
                                                            'value' => Url::to('index.php?r=aitk/request/detail&id=' . $model->request_id),
                                                ]);
                                            },
                                                    'cancel' => function ($url, $model) {

//                                        if ($model->status_dosen==1)
                                                return Html::a(
                                                                '<span class="glyphicon glyphicon-remove"></span>', $url, [
                                                            'title' => Yii::t('yii', 'Cancel Request'),
                                                            'data-confirm' => Yii::t('yii', 'Are you sure to  Cancel Your Request? '),
                                                            'data-method' => 'post',
                                                ]);
                                            },
//                                                    'update' => function ($url, $model) {
//                                                if ($model->status_dosen != 1)
//                                                    return Html::a(
//                                                                    '<span class="glyphicon glyphicon-pencil"></span>', $url, [
//                                                                'title' => Yii::t('yii', 'Update Request'),
//                                                                'data-confirm' => Yii::t('yii', 'Are you sure to  Update Your Request? '),
//                                                                'data-method' => 'post',
//                                                    ]);
//                                            }
                                                ],
                                                'urlCreator' => function ($action, $model, $key, $index) {
                                            if ($action === 'cancel') {

//                                         if ($data->status_dosen != 1)

                                                $url = Url::toRoute(['cancelrequest', 'id' => $model->request_id]); // your own url generation logic
                                                return $url;
                                            }

//                                            if ($action === 'update') {
//                                                $url = Url::toRoute(['edit', 'id' => $model->request_id]); // your own url generation logic
//                                                return $url;
//                                            }
                                        }
                                            ],
                                        ],
                                    ]);
                                    ?>
                                    <?php Pjax::end() ?>
                                </div>



                            </div>
                        </div>
                    </div>


                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseApproved">Approved Request</a>
                            </h4>
                        </div>
                        <div id="collapseApproved" class="panel-collapse collapse">
                            <div class="panel-body">

                                <div class="col-lg-12">
                                    <!--<h2>Approved</h2>-->


                                    <?php
                                    Pjax::begin();
                                    $this->registerJs('
                                        $(".printPreview").click(function() {
                                            $("#modalPrintPreview").modal("show").
                                            find("#modalPrint").
                                            load($(this).attr("value"))
                                            });

                                            ', \yii\web\View::POS_READY);




                                    echo GridView::widget([
                                        'dataProvider' => $dataProviderApproved,
                                        'showOnEmpty' => false,
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
//                                    [
//                                        'attribute' => 'mahasiswa_id',
//                                        'value' => 'mahasiswa.nama_mahasiswa',
//                                    ],
                                            [
                                                'attribute' => 'waktu_start',
                                                'label' => 'Mulai',
                                            ],
                                            [
                                                'attribute' => 'waktu_end',
                                                'label' => 'Selesai',
                                            ],
                                            'alasan_ijin',
//                                            'alasan_penolakan',
                                            [
                                                'attribute' => 'status_dosen',
                                                'format' => 'raw',
                                                'value' => function($data) {
                                                    //  return $data->status_dosen == 1 ? "Approved" : ($data->status_dosen == NULL ? "Pending" : "Rejected");
                                                    return $data->status_dosen === 1 ? "<span class='label label-success'>Approved</span>" : ($data->status_dosen === 0 ? "<span class='label label-danger'> Rejected </span>" : "<span class='label label-warning'>Pending </span>");
                                                }
                                            ],
                                            [
                                                'attribute' => 'status_asrama',
                                                'format' => 'raw',
                                                'value' => function($data) {
                                                    return $data->status_asrama === 1 ? "<span class='label label-success'>Approved</span>" : ($data->status_asrama === 0 ? "<span class='label label-danger'> Rejected </span>" : "<span class='label label-warning'>Pending</span>");
                                                }
                                            ],
                                            [

                                                'class' => 'yii\grid\ActionColumn',
                                                'template' => '{view} {print}',
                                                'buttons' => [

                                                    'view' => function ($url, $model) {

                                                        return Html::a(
                                                                        '<span class="glyphicon glyphicon-eye-open"></span>', "#", [
                                                                    'title' => Yii::t('yii', 'View Detail'),
                                                                    'class' => 'detailView',
                                                                    'value' => Url::to('index.php?r=aitk/request/detail&id=' . $model->request_id),
                                                        ]);
                                                    },
                                                            'print' => function ($url, $model) {

                                                        return Html::a(
                                                                        '<span class="glyphicon glyphicon-print"></span>', '#', [
                                                                    'title' => Yii::t('yii', 'Print'),
                                                                    //'data-confirm' => Yii::t('yii', 'Download Form ?'),
                                                                    //'data-method' => 'post',
                                                                    'class' => 'printPreview',
                                                                    'value' => Url::to('index.php?r=aitk/request/printpreview&id=' . $model->request_id),
                                                        ]);
                                                    },
                                                        ],
//                                                        'urlCreator' => function($action, $model, $key, $index) {
//                                                    if ($action === 'print') {
//                                                        $url = Url::toRoute([ 'print', 'id' => $model->request_id]); 
//
//                                                        return $url;
//                                                    }
//                                                }
                                                    ],
                                                ],
                                            ]);

                                            Pjax::end();
                                            ?> 
                                        </div>



                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-danger">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseRejected">Rejected Request</a>
                                    </h4>
                                </div>
                                <div id="collapseRejected" class="panel-collapse collapse">
                                    <div class="panel-body">

                                        <div class="row">

                                            <div class="col-lg-12">

                                                <?php
                                                Pjax::begin();
                                                $this->registerJs('
                                                $(".printPreview").click(function() {
                                                    $("#modalPrintPreview").modal("show").
                                                    find("#modalPrint").
                                                    load($(this).attr("value"))
                                                    });

                                                    ', \yii\web\View::POS_READY);


                                                echo GridView::widget([
                                                    'dataProvider' => $dataProviderRejected,
                                                    'showOnEmpty' => false,
//        'filterModel' =>$searchRejected,
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
                                                        'alasan_penolakan',
                                                        [
                                                            'attribute' => 'status_dosen',
                                                            'format' => 'raw',
                                                            'value' => function ($data) {
                                                                //  return $data->status_dosen == 1 ? "Approved" : ($data->status_dosen == NULL ? "Pending" : "Rejected");
                                                                return $data->status_dosen === 1 ? "<span class='label label-success'>Approved</span>" : ($data->status_dosen === 0 ? "<span class='label label-danger'> Rejected </span>" : "<span class='label label-warning'>Pending </span>");
                                                            }
                                                        ],
                                                        [
                                                            'attribute' => 'status_asrama',
                                                            'format' => 'raw',
                                                            'value' => function ($data) {
                                                                return $data->status_asrama === 1 ? "<span class='label label-success'>Approved</span>" : ($data->status_asrama === 0 ? "<span class='label label-danger'> Rejected </span>" : "<span class='label label-warning'>Pending</span>");
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
                                                                    ]
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

                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseRequest">All Request</a>
                                            </h4>
                                        </div>
                                        <div id="collapseRequest" class="panel-collapse collapse">
                                            <div class="panel-body">

                                                <div class="col-lg-12">
                                                    <!--<h2>All Request</h2>-->

                                                    <?php
                                                    Pjax::begin();
                                                    $this->registerJs('
                                                    $(".printPreview").click(function() {
                                                        $("#modalPrintPreview").modal("show").
                                                        find("#modalPrint").
                                                        load($(this).attr("value"))
                                                        });

                                                        ', \yii\web\View::POS_READY);
                                                    ?>
                                                    <?=
                                                    GridView::widget([
                                                        'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
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
                                                                'template' => '{view} {print}',
                                                                'buttons' => [

                                                                    'view' => function ($url, $model) {

                                                                        return Html::a(
                                                                                        '<span class="glyphicon glyphicon-eye-open"></span>', "#", [
                                                                                    'title' => Yii::t('yii', 'View Detail'),
                                                                                    'class' => 'detailView',
                                                                                    'value' => Url::to('index.php?r=aitk/request/detail&id=' . $model->request_id),
                                                                        ]);
                                                                    },
                                                                            'print' => function ($url, $model) {
                                                                        if ($model->status_dosen == 1 && $model->status_asrama == 1) {
                                                                            return Html::a(
                                                                                            '<span class="glyphicon glyphicon-print"></span>', '#', [
                                                                                        'title' => Yii::t('yii', 'Print'),
                                                                                        //'data-confirm' => Yii::t('yii', 'Download Form ?'),
                                                                                        //'data-method' => 'post',
                                                                                        'class' => 'printPreview',
                                                                                        'value' => Url::to('index.php?r=aitk/request/printpreview&id=' . $model->request_id),
                                                                            ]);
                                                                        }
                                                                    },
                                                                        ],
                                                                    ]
                                                                ],
                                                            ]);
                                                            ?>
                                                            <?php Pjax::end(); ?>


                        </div>
                    </div>
                </div>
            </div>






        </div>



    </div>
</div>
