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
//
$this->title = 'Print Preview';
$this->params['breadcrumbs'][] = $this->title;

?>
<?= Html::a('Print', ['print','id'=>$model->request_id], ['class' => 'btn btn-info']) ?>

 <?= $this->render('_printPreview', [
        'model' => $model,
    ]) ?>