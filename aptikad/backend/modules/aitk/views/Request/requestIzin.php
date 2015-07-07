<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\aitk\models\AitkRequest */
/* @var $count backend\modules\aitk\models\AitkRequest */
/* @var $arrMatkul backend\modules\aitk\models\AitkRequest */
/* @var $matkulId backend\modules\aitk\models\AitkRequest */

$this->title = 'Request Izin';
$this->params['breadcrumbs'][] = ['label' => 'Aptikad Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="aitk-request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_requestIzin', [
        'model' => $model,
        'count' => $count,
        'arrMatkul' => $arrMatkul,
        'matkulId' => $matkulId,
        'mahasiswaLogin'=>$mahasiswaLogin
    ])
    ?>

</div>
