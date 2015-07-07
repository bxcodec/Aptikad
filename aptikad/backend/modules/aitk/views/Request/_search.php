<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\aitk\models\search\AitkRequestSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="aitk-request-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'request_id') ?>

    <?= $form->field($model, 'dosen_wali') ?>

    <?= $form->field($model, 'requester') ?>

    <?= $form->field($model, 'mahasiswa_id') ?>

    <?= $form->field($model, 'tujuan_sms_pengurus') ?>

    <?php // echo $form->field($model, 'pengurus_asrama') ?>

    <?php // echo $form->field($model, 'tipe_ijin') ?>

    <?php // echo $form->field($model, 'waktu_start') ?>

    <?php // echo $form->field($model, 'waktu_end') ?>

    <?php // echo $form->field($model, 'alasan_ijin') ?>

    <?php // echo $form->field($model, 'lampiran') ?>

    <?php // echo $form->field($model, 'status_asrama') ?>

    <?php // echo $form->field($model, 'status_dosen') ?>

    <?php // echo $form->field($model, 'deleted') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
