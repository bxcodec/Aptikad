<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
//use yii\widgets\ActiveForm;
use kartik\field;
use kartik\builder\Form;
use yii\kartik\ActiveForm;
use backend\modules\aitk\models\AitkRKelas;
use backend\modules\aitk\models\AitkRMahasiswa;
use yii\bootstrap\ActiveField;
use kartik\widgets\Typeahead;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\modules\aitk\models\AitkRequest */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="aitk-request-form">
    <?php $form = kartik\form\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'method' => 'GET']]); ?>

    <style>


        #searchNama {
            width:40%;
            float:left;
            margin-right: 20px;
        }

        #w1{
            width:20%;
            float:left;
        }
        .form-group{
            float:none;
            clear:both;
        }
    </style>
    <?php
    $this->registerJs('
        
        $(document).ready (function() {
            $("#nameMhs").change(function () {
            $("#kelasDropDown").attr("disabled", false);
            });

        }); 
', yii\web\View::POS_READY);
    ?>

    <script>
    </script>

    <?php
    echo ' <div id=searchNama> <label class="control-label">Search By Nama</label>';
    echo Typeahead::widget([
        'name' => 'FormSearchReport[nama_mahasiswa]',
        'options' => [
            'placeholder' => 'Ketik Nama Mahasiswa',
            'id' => 'nameMhs',
        ],
        'scrollable' => true,
        'pluginOptions' => ['highlight' => true],
        'dataset' => [
            [
                'prefetch' => Url::to(['request/mahasiswalist']),
                'limit' => 10
            ]
        ],
        'pluginEvents' => [
            'typeahead:selected' => 'function() {
                $("#kelasDropDown").attr("disabled" , true);
                $("#kelasDropDown").attr("value" , "");
                }',
        ]
    ]);
    echo '</div>'
    ?>

    <?php
    echo Form::widget([
        'model' => $model,
        'form' => $form,
        'columns' => 4,
        'attributes' => [

            'kelas' => [
                'label' => 'Search By Kelas',
                'type' => Form::INPUT_DROPDOWN_LIST,
                'items' => ArrayHelper::map(AitkRKelas::find()->all(), 'kelas_id', 'kode_kelas'),
                'options' => [
                    'id' => 'kelasDropDown',
                    'prompt' => '---',
                    'onchange' => '
                        var val =(this.value);
                        if(val!="") {
                        $("#nameMhs").attr("disabled", true);
                        }
                        else {
                        $("#nameMhs").attr("disabled", false);
                        }           
                    ']
            ]
        ]
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php $form = kartik\form\ActiveForm::end(); ?>


</div>
