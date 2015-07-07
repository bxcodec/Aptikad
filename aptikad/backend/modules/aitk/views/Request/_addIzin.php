<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\builder\Form;
use yii\helpers\ArrayHelper;
use backend\modules\aitk\models\AitkRDosen;
use backend\modules\aitk\models\Outbox;
use backend\modules\aitk\models\Inbox;
use backend\modules\aitk\models\AitkRAsrama;
use backend\modules\aitk\models\AitkRMahasiswa;
use backend\modules\aitk\models\AitkDosenmatakuliah;
use backend\modules\aitk\models\AitkRMatakuliah;
use backend\modules\aitk\models\AitkRKelas;
use kartik\widgets\Typeahead;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model backend\modules\aitk\models\AitkRequest */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="aitk-request-form">
    <style>
        #dataMahasiswa {
            display: none;
            position: relative;
        }
    </style>


    <?php
    $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
                'id' => 'form-request',
    ]);
    ?>
    <!--
        <script>
            function handleMatakuliah(nama) {
    
                var name = nama.split(" ")[0];
    
                $('#ck' + name).click(function () {
                    $("." + name).remove();
                    $('.detailMatakuliah:first').clone().insertAfter(".detailMatakuliah").prop({class: name, id: name, style: "display:block"});
                    $(".control-label").prop("id", "lbl"+name);
                    if ($("#ck" + name).is(":checked")) {
                        $(".detailMatakuliah").hide();
                        
                        $("." + name).show();
                            
                    }
                    else {
                        $("." + name).remove();
    
                    }
    
    
                });
    
    
            }
    
        </script>-->

    <?php
    echo '<label class="control-label">Nama Mahasiswa</label>';
    echo Typeahead::widget([
        'name' => 'FormIzin[nama_mahasiswa]',
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
            var nama = $(this).val();
            
            $.getJSON("index.php?r=aitk/request/getmahasiswa",{name:nama}, function(data) {
             $("#dataMahasiswa").show();
             $("#namaMahasiswa").replaceWith("<p id =namaMahasiswa>"+data[0].nama_mahasiswa + "</p>");
             $("#nimMahasiswa").replaceWith("<p id =nimMahasiswa>"+data[0].nim + "</p>");
             $("#kelasMahasiswa").replaceWith("<p id =kelasMahasiswa>"+data[0].kelas + "</p>");
             $("#semesterMahasiswa").replaceWith("<p id =semesterMahasiswa>"+data[0].semester + "</p>");
             $("#waliMahasiswa").replaceWith("<p id =waliMahasiswa>"+data[0].wali + "</p>");
             $(".matakuliahIzin").replaceWith("<div class=matakuliahIzin style=\" display:none\"> </div>");
             $("#Krad").attr("checked" , false);$("#Srad").attr("checked" , false);

             $(".matakuliahIzin").append("<label class=\"control-label\" for=FormIzin[matakuliahList][]> Matakuliah Yang Tidak Di Ikuti</label> <br/> <br/>");
            $.each( data[0].matakuliah, function( key, value ) {
            $(".matakuliahIzin").append("<input  id=ck"+value.matakuliah.split(" ")[0] +" type=checkbox value ="+value.matakuliah+" name=FormIzin[matakuliahList][] class=mtkuliah ><label class=\"control-label\" for= "+value.matakuliah+" >"+value.matakuliah+"</label> </input>");
                        }); 

    });

}',
//            'typeahead:autocompleted' => 'function() {alert("halo");}'
        ]
    ]);


    echo '<label class="control-label">Waktu Izin</label>';

    echo DateRangePicker::widget([
        'model' => $model,
        'attribute' => 'tanggal',
        'convertFormat' => true,
        'pluginOptions' => [
            'timePicker' => true,
            'hideInput' => true,
            'separator' => ' s/d ',
            'timePickerIncrement' => 10,
            'format' => 'Y-m-d H:i'
        ]
    ]);

    echo Form::widget(
            [
                'model' => $model,
                'form' => $form,
                'columns' => 1,
                'attributes' => [

                    'alasan_ijin' => [
                        'label' => 'Alasan',
                        'type' => Form::INPUT_TEXTAREA,
                        'options' => ['placeholder' => 'Max 30 Characters', 'maxlength' => 30]
                    ],
    ]]);

    echo Form::widget(
            [
                'model' => $model,
                'form' => $form,
                'columns' => 2,
                'attributes' => [

                    'lampiran' => [
                        'label' => 'Nama Lampiran',
                        'type' => Form::INPUT_TEXT,
                    ],
                    'file_lampiran'=> [
                        'label' => 'Bukti Lampiran',
                        'type' => Form::INPUT_FILE,
                    ],
                ],
            ]
    );
    ?>


    <div class="panel panel-info" id="dataMahasiswa" >
        <!-- Default panel contents -->
        <div class="panel-heading">Data Mahasiswa</div>

        <!-- Table -->
        <table class="table">

            <tr><td>Nama</td> <td>:</td> <td><p id="namaMahasiswa">Nama Mahasiswa</p></td></tr>

            <tr><td>NIM</td> <td>:</td> <td><p id="nimMahasiswa">Nama Mahasiswa</p></td></tr>

            <tr><td>Kelas</td> <td>:</td> <td><p id="kelasMahasiswa">Kelas Mahasiswa</p></td></tr>

            <tr><td>Semester</td> <td>:</td> <td><p id="semesterMahasiswa">Semester Mahasiswa</p></td></tr>

            <tr><td>Wali</td> <td>:</td> <td><p id="waliMahasiswa">Wali Mahasiswa</p></td></tr>

        </table>
    </div>



    <div class="matakuliahIzin" style="display: block">

    </div>






    <div class="form-group">
<?= Html::submitButton(isset($model) ? 'Create' : 'Update', ['class' => isset($model) ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php kartik\form\ActiveForm::end(); ?>



<?php
$this->registerJs('$(document).ready(function(){
    $(\'input[name="FormIzin[tipe_ijin]"]\').change(function(){

            if ($(this).val() ==\'K\'){
                $(\'.matakuliahIzin\').css("display","none")
                $(\'.keluarKampus\').css("display","block")
                }
            else
                { 
                $(\'.keluarKampus\').css("display","none")
                $(\'.matakuliahIzin\').css("display","block")
                }
            });
            });', \yii\web\View::POS_READY);

$this->registerJs(' $(document).ready(function(){
    if($("#Srad").is(":checked")) {
//                $(\'.keluarKampus\').css("display","none")
                $(\'.matakuliahIzin\').css("display","block")
        
        }

            });', \yii\web\View::POS_READY);
?>


</div>

