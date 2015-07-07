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

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\aitk\models\search\AitkRequestSearch */
/* @var $dataProviderPending yii\data\ActiveDataProvider */
/* @var $dataProviderRejected yii\data\ActiveDataProvider */
/* @var $dataProviderRequest yii\data\ActiveDataProvider */
/* @var $dataProviderApproved yii\data\ActiveDataProvider */




$this->title = 'List Of Request';
$this->params['breadcrumbs'][] = $this->title;

//Pjax::begin();

$this->registerJs('
    $("#btnAdd").click(function() {
        $("#modalAdd").modal("show").
        find("#modalcon").
        load($(this).attr("value"))
        });

        ', \yii\web\View::POS_READY);
//Pjax::end();
?>
<div class="aitk-request-index">

    <?php foreach (Yii::$app->session->getAllFlashes() as $message):; ?>
        <?php
        echo \kartik\widgets\Growl::widget([
            'type' => (!empty($message['type'])) ? $message['type'] : 'danger',
            'title' => (!empty($message['title'])) ? Html::encode($message['title']) : 'Title Not Set!',
            'icon' => (!empty($message['icon'])) ? $message['icon'] : 'fa fa-info',
            'body' => (!empty($message['message'])) ? Html::encode($message['message']) : 'Message Not Set!',
            'showSeparator' => true,
            'delay' => 1, //This delay is how long before the message shows
            'pluginOptions' => [
                'showProgressbar' => true,
                'delay' => (!empty($message['duration'])) ? $message['duration'] : 10000, //This delay is how long the message shows for
                'placement' => [
                    'from' => (!empty($message['positonY'])) ? $message['positonY'] : 'top',
                    'align' => (!empty($message['positonX'])) ? $message['positonX'] : 'right',
                ]
            ]
        ]);
        ?>
    <?php endforeach; ?>




    <h1><?= Html::encode($this->title) ?></h1>
    <?php
//    Pjax::begin();
    Modal::begin([
        'header' => 'Alasan Penolakan',
        'id' => 'modalTolak',
        'size' => 'modal-md'
    ]);

    echo "<div id= 'modalContent'></div>";

    Modal::end();
    Modal::begin([
        'header' => 'Add Request',
        'id' => 'modalAdd',
        'size' => 'modal-md'
    ]);

    echo "<div id= 'modalcon'></div>";

    Modal::end();

    Modal::begin([
        'header' => 'Detail Request',
        'id' => 'modalDetail',
        'size' => 'modal-md'
    ]);

    echo "<div id= 'modalDetailContent'></div>";

    Modal::end();

    Modal::begin([
        'header' => 'Email',
        'id' => 'modalEmail',
        'size' => 'modal-md'
    ]);

    echo "<div id= 'modalEmailContent'></div>";

    Modal::end();
//    Pjax::end();
    ?>

    <p>
        <?= Html::button('Add Request', ['value' => Url::to('index.php?r=aitk/request/add'), 'class' => 'btn btn-info', 'id' => 'btnAdd']) ?>


    </p>


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
                                    $(".rejectAsrama").click(function() {
                                        $("#modalTolak").modal("show").
                                        find("#modalContent").
                                        load($(this).attr("value"))
                                //        alert("aaaaa");
                                        });

                                        ', \yii\web\View::POS_READY);


                            $this->registerJs('
                                    $(".detailView").click(function() {
                                        $("#modalDetail").modal("show").
                                        find("#modalDetailContent").
                                        load($(this).attr("value"))
                                        });

                                        ', \yii\web\View::POS_READY);

                            $this->registerJs('
                                    $(".kirimEmail").click(function() {
                                        $("#modalEmail").modal("show").
                                        find("#modalEmailContent").
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
                                        'template' => '{view} {approveasrama} {rejectasrama}',
                                        'buttons' => [

                                            'view' => function ($url, $model) {

                                                return Html::a(
                                                                '<span class="glyphicon glyphicon-eye-open"></span>', "#", [
                                                            'title' => Yii::t('yii', 'View Detail'),
                                                            'class' => 'detailView',
                                                            'value' => Url::to('index.php?r=aitk/request/detail&id=' . $model->request_id),
                                                ]);
                                            },
                                                    'approveasrama' => function ($url, $model) {

                                                return Html::a(
                                                                '<span class="glyphicon glyphicon-ok"></span>', $url, [
                                                            'title' => Yii::t('yii', 'Approve Request'),
//                                                    'data-confirm' => Yii::t('yii', 'Download Form ?'),
                                                            'data-method' => 'post',
                                                ]);
                                            },
                                                    'rejectasrama' => function ($url, $model) {

                                                return Html::a(
                                                                '<span class="glyphicon glyphicon-remove"></span>', "#", [
                                                            'title' => Yii::t('yii', 'Reject Request'),
                                                            'class' => 'rejectAsrama',
                                                            'value' => Url::to('index.php?r=aitk/request/alasanreject&id=' . $model->request_id),
                                                ]);
                                            },
                                                ],
                                                'urlCreator' => function ($action, $model, $key, $index) {
                                            if ($action === 'approveasrama') {
//                $url =Yii::$app->getUrlManager()->createUrl(['aitk/approvedosen' , 'value' =>1 ,'id' =>$model->request_id]); // your own url generation logic
                                                $url = Url::toRoute(['approveasrama', 'value' => 1, 'id' => $model->request_id]); // your own url generation logic
                                                return $url;
                                            }
//                                    if ($action === 'rejectasrama') {
//                                        $url = Url::toRoute("#");
//                                        return $url;
//                                    }

                                            if ($action === 'view') {
                                                $url = Url::toRoute(['view', 'id' => $model->request_id]);
                                                return $url;
                                            }
                                        }
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
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseApproved"> Approved Request</a>
                        </h4>
                    </div>
                    <div id="collapseApproved" class="panel-collapse collapse">
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

                                    echo GridView::widget([
                                        'dataProvider' => $dataProviderApproved,
                                        'showOnEmpty' => false,
                                        'columns' => [
                                            ['class' => 'yii\grid\SerialColumn'],
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
                                                'template' => '{view} {sendmail}',
                                                'buttons' => [

                                                    'view' => function ($url, $model) {

                                                        return Html::a(
                                                                        '<span class="glyphicon glyphicon-eye-open"></span>', "#", [
                                                                    'title' => Yii::t('yii', 'View Detail'),
                                                                    'class' => 'detailView',
                                                                    'value' => Url::to('index.php?r=aitk/request/detail&id=' . $model->request_id),
                                                        ]);
                                                    },
                                                            'sendmail' => function ($url, $model) {

                                                        return Html::a(
                                                                        '<span class="glyphicon glyphicon-envelope"></span>', "#", [
                                                                    'title' => Yii::t('yii', 'Send Email To Dosen Staff'),
                                                                    'value' => Url::to('index.php?r=aitk/request/sendmail&id=' . $model->request_id),
                                                                    'class' => 'kirimEmail'
                                                        ]);
                                                    },
                                                        ],
                                                        'urlCreator' => function ($action, $model, $key, $index) {
                                                    if ($action === 'sendmail') {
                                                        $url = Url::toRoute(['sendmail', 'id' => $model->request_id]); // your own url generation logic
                                                        return $url;
                                                    }
                                                }
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
                                                'dataProvider' => $dataProviderRequest,
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
                                                        'template' => '{view} {sendmail}',
                                                        'buttons' => [

                                                            'view' => function ($url, $model) {

                                                                return Html::a(
                                                                                '<span class="glyphicon glyphicon-eye-open"></span>', "#", [
                                                                            'title' => Yii::t('yii', 'View Detail'),
                                                                            'class' => 'detailView',
                                                                            'value' => Url::to('index.php?r=aitk/request/detail&id=' . $model->request_id),
                                                                ]);
                                                            },
                                                                    'sendmail' => function ($url, $model) {
                                                                if ($model->status_dosen == 1 && $model->status_asrama == 1) {

                                                                    return Html::a(
                                                                                    '<span class="glyphicon glyphicon-envelope"></span>', "#", [
                                                                                'title' => Yii::t('yii', 'Send Email To Dosen Staff'),
                                                                                'value' => Url::to('index.php?r=aitk/request/sendmail&id=' . $model->request_id),
                                                                                'class' => 'kirimEmail'
                                                                    ]);
                                                                }
                                                            },
                                                                ],
                                                            ],
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
