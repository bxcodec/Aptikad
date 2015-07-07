<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\aitk\models\AitkRequest */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="aitk-request-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'dosen_wali')->textInput() ?>

    <?= $form->field($model, 'requester')->textInput() ?>

    <?= $form->field($model, 'mahasiswa_id')->textInput() ?>

    <?= $form->field($model, 'tujuan_sms_pengurus')->textInput() ?>

    <?= $form->field($model, 'pengurus_asrama')->textInput() ?>

    <?= $form->field($model, 'tipe_ijin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'waktu_start')->textInput() ?>

    <?= $form->field($model, 'waktu_end')->textInput() ?>

    <?= $form->field($model, 'alasan_ijin')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'lampiran')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status_asrama')->textInput() ?>

    <?= $form->field($model, 'status_dosen')->textInput() ?>

    <?= $form->field($model, 'deleted')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'updated_by')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
