<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\aitk\models\AitkRequest */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Edit Email';
//$this->params['breadcrumbs'][] = ['label' => 'Aptikad Requests', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>

<div class="aitk-request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_editEmail', [
        'model' => $model,
    ])
    ?>

</div>