<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\aitk\models\AitkRequest */

$this->title = 'Add Request For Student';
$this->params['breadcrumbs'][] = ['label' => 'Aptikad Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="aitk-request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_addIzin', [
        'model' => $model,
    ]) ?>

</div>
