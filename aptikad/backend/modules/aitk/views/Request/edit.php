<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\aitk\models\AitkRequest */

$this->title = 'Update Aitk Request: ' . ' ' . $model->request_id;
$this->params['breadcrumbs'][] = ['label' => 'Aitk Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->request_id, 'url' => ['view', 'id' => $model->request_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="aitk-request-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    if ($level > 0) :
        ?>
        <?=
        $this->render('_editRequestMahasiswa', [
            'model' => $model,
        ])
        ?>
        <?php
    endif;
    ?>
    
    <?=
    $this->render('_editByAsrama', [
        'model' => $model,
    ])
    ?>


</div>
