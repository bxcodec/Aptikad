<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\aitk\models\AitkRequest */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="aitk-request-form">

    <?php $form = ActiveForm::begin(); ?>

    
    <?= $form->field($model, 'alasan_penolakan')->textarea(['rows' => 6]) ?>

    
    <div class="form-group">
        <?= Html::submitButton('Reject', ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
