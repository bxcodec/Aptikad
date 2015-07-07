<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
//use yii\widgets\ActiveForm;
use yii\kartik\ActiveForm;
use kartik\builder\Form;
use yii\bootstrap\ActiveField;
use backend\modules\aitk\models\AitkRDosen;
use backend\modules\aitk\models\AitkRMahasiswa;
use backend\modules\aitk\models\AitkRMatakuliah;
use backend\modules\aitk\models\AitkRAsrama;
use kartik\field;
use backend\modules\aitk\models\AitkDosenmatakuliah;
use backend\modules\aitk\models\AitkRKelas;

/* @var $this yii\web\View */
/* @var $model backend\modules\aitk\models\FormIzin */
/* @var $count backend\modules\aitk\models\FormIzin */
/* @var $arrMatkul backend\modules\aitk\models\AitkRequest */
/* @var $matkulId backend\modules\aitk\models\AitkRequest */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="aitk-request-form">

    <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading">Data Mahasiswa</div>

        <!-- Table -->
        <table class="table">

            <tr><td>Nama</td> <td>:</td> <td><?php echo $model->nama_mahasiswa ?></td></tr>

            <tr><td>NIM</td> <td>:</td> <td><?php echo $model->nim ?></td></tr>

            <tr><td>Kelas</td> <td>:</td> <td><?php echo $model->kelas ?></td></tr>

            <tr><td>Wali</td> <td>:</td> <td><?php echo $model->dosen_wali ?></td></tr>

        </table>
    </div>

    <?php
    $nim = $model->nim;
    $nama = $model->nama_mahasiswa;

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
                                $(\'.keluarKampus\').css("display","none")
                                $(\'.matakuliahIzin\').css("display","block")
                        }

                        });', \yii\web\View::POS_READY);
    ?>





    <?php $form = kartik\form\ActiveForm::begin(); ?>




    <?php
    echo Form::widget(
            [
                'model' => $model,
                'form' => $form,
                'columns' => 2,
                'attributes' => [
//                    'dosen_wali' => [
//                        'label' => 'Dosen Wali',
//                        'type' => Form::INPUT_DROPDOWN_LIST,
//                        'items' => ArrayHelper::map(AitkRDosen::find()->where(['iswali' => 1])->all(), 'dosen_id', 'nama_dosen'),
//                        'options' => ['inline' => true, 'prompt' => 'Pilih Wali sesuai']
//                    ],
                    'tujuan_sms' => [
                        'label' => 'Pengurus Asrama',
                        'type' => Form::INPUT_DROPDOWN_LIST,
                        'items' => ArrayHelper::map(AitkRAsrama::find()->all(), 'asrama_id', 'nama_pengurus'),
                        'options' => ['inline' => true, 'prompt' => 'Pilih Tujuan SMS asrama']
                    ],
    ]]);
    ?>

    <?php
    echo Form::widget(
            [
                'model' => $model,
                'form' => $form,
                'columns' => 2,
                'attributes' => [


                    'tipe_ijin' => [
                        'type' => Form::INPUT_RADIO_LIST,
                        'items' => ['K' => 'Keluar Kampus', 'S' => 'Tidak Hadir/Sakit'],
                        'options' => ['inline' => true,
                            'item' =>
                            function ($index, $label, $name, $checked, $value) {
                        return Html::radio($name, $checked, [
                                    'value' => $value,
                                    'label' => '<label for="' . $label . '">' . $label . '</label>',
                                    'labelOptions' => [
                                        //'class' => 'ckbox ckbox-primary checkbox-inline',
                                        'id' => $value . 'rad',
                                        'style' => 'display:block; float:left'
                                    ],
                                    'id' => $value,
                                    'class' => 'PilihanIzinList',
                        ]);
                    }
                        ]
                    ],
                    'waktuKeluar' => [
                        'label' => 'Tanggal ',
                        'attributes' => [
                            'tanggal_mulai' => [
                                'type' => Form::INPUT_WIDGET,
                                'widgetClass' => '\kartik\widgets\DateTimePicker',
                                'options' => ['options' => ['placeholder' => 'Time from...', 'id' => 'timeStart' . 'mulaiKeluar']],
                            ],
                            'tanggal_selesai' => [
                                'type' => Form::INPUT_WIDGET,
                                'widgetClass' => '\kartik\widgets\DateTimePicker',
                                'options' => ['options' => ['placeholder' => 'Time to...', 'id' => 'timeEnd' . 'Keluar']]
                            ],
                        ]
                    ],
                ]
    ]);
    $semester = $model->semester;
    ?>

    <div class="keluarKampus" style="display: none" >


    </div>







    <div class="matakuliahIzin" style="display:none">



        <?=
        $form->field($model, 'matakuliahList')->checkboxList(
                ArrayHelper::map(AitkRMatakuliah::find()->where(['semester' => $semester])->all(), 'matakuliah_id', 'matakuliah'), [
            'item' =>
            function ($index, $label, $name, $checked, $value) {
        return Html::checkbox($name, $checked, [
                    'value' => $value,
                    'label' => '<label for="' . $label . '">' . $label . '</label>',
                    'labelOptions' => [
                        'class' => 'ckbox ckbox-primary checkbox-inline',
                    ],
                    'id' => current(explode(' ', $label)),
                    'class' => 'matakuliahIzinList',
        ]);
    }
        ]);




        for ($i = 0; $i < $count; $i++) {

            $this->registerJs('$(\'' . '#' . current(explode(' ', $arrMatkul[$i])) . '\').click(function(){
            if($("' . '#' . current(explode(' ', $arrMatkul[$i])) . '").is(\':checked\')) {
                $(".jamMatakuliah").show();  // checked
                $("#sesi' . current(explode(' ', $arrMatkul[$i])) . '").show();  // checked
                }   
            else {
               $(".matakuliahIzinList").each(function() {
                    $("#sesi' . current(explode(' ', $arrMatkul[$i])) . '").hide();  // checked
                
                });
                }
         
            });', \yii\web\View::POS_READY);

            $this->registerJs('$(\'' . '#SesiP_' . current(explode(' ', $arrMatkul[$i])) . '\').click(function(){
            if($("' . '#SesiP_' . current(explode(' ', $arrMatkul[$i])) . '").is(\':checked\')) {
//                $(".jamMatakuliah").show();  // checked
                $("#matkulP' . current(explode(' ', $arrMatkul[$i])) . '").show();  // checked
                }
            else {
               $(".PilihanSesiList").each(function() {
                $("#matkulP' . current(explode(' ', $arrMatkul[$i])) . '").hide();  // checked
                
                });
                }
         
            });', \yii\web\View::POS_READY);


            $this->registerJs('$(\'' . '#SesiT_' . current(explode(' ', $arrMatkul[$i])) . '\').click(function(){
            if($("' . '#SesiT_' . current(explode(' ', $arrMatkul[$i])) . '").is(\':checked\')) {
//                $(".jamMatakuliah").show();  // checked
                $("#matkulT' . current(explode(' ', $arrMatkul[$i])) . '").show();  // checked
                }
            else {
               $(".PilihanSesiList").each(function() {
                $("#matkulT' . current(explode(' ', $arrMatkul[$i])) . '").hide();;  // checked
                
                });
                   
                }
         
            });', \yii\web\View::POS_READY);
        }
        ?>    




        <div class="jamMatakuliah" style="display: none" >


            <?php
            $j = 0;
            for ($i = 0; $i < $count; $i++) {

                $dosenMatakuliah = AitkDosenmatakuliah::find()->where(['matakuliah_id' => $matkulId[$i]])->all();

                $arrDosenId = array();
                foreach ($dosenMatakuliah as $valueDosen => $keyD) {
                    foreach ($keyD as $valD => $isiD)
                        if ($valD == "dosen_id") {
                            $arrDosenId [] = $keyD[$valD];
                        }
                }

                $dosenAllMatakuliah = AitkRDosen::findAll($arrDosenId);

                echo "<div id=\"sesi" . current(explode(' ', $arrMatkul[$i])) . "\" style=\"display:none;\">";
                echo kartik\builder\Form::widget([
                    'model' => $model,
                    'form' => $form,
                    'columns' => 2,
                    'attributes' => [
                        'sesiList[' . $i . '][]' => [
                            'label' => 'Sesi ' . $arrMatkul[$i],
                            'type' => Form::INPUT_CHECKBOX_LIST,
                            'items' => array('T_' . current(explode(' ', $arrMatkul[$i])) => 'Teori', 'P_' . current(explode(' ', $arrMatkul[$i])) => 'Praktikum'),
                            'options' => ['inline' => true,
                                'item' =>
                                function ($index, $label, $name, $checked, $value) {
                            return Html::checkbox($name, $checked, [
                                        'value' => $value,
                                        'label' => '<label for="' . $label . '">' . $label . '</label>',
                                        'labelOptions' => [
                                            'id' => $value . 'SesiRad',
                                            'style' => 'display:block; float:left'
                                        ],
                                        'id' => "Sesi" . $value,
                                        'class' => 'PilihanSesiList',
                            ]);
                        }
                            ]
                        ]
                ]]);


                echo "<div id=\"matkulT" . current(explode(' ', $arrMatkul[$i])) . "\" style=\"display:none;\">";



                echo kartik\builder\Form::widget([
                    'model' => $model,
                    'form' => $form,
                    'columns' => 2,
                    'attributes' => [
                        'waktuKuliah' => [
                            'label' => 'Waktu Sesi Teori ' . $arrMatkul[$i],
                            'attributes' => [
                                'waktu_mulaiKulList[' . $i . '][]' => [
                                    'type' => Form::INPUT_WIDGET,
                                    'widgetClass' => '\kartik\widgets\TimePicker',
                                    'options' => ['options' => [
                                            'placeholder' => 'Time from...',
                                            'id' => 'timeStartTeori' . current(explode(' ', $arrMatkul[$i])),
                                            'options' => ['required' => true]
                                        ]
                                    ],
                                ],
                                'waktu_selesaiKulList[' . $i . '][]' => [
                                    'type' => Form::INPUT_WIDGET,
                                    'widgetClass' => '\kartik\widgets\TimePicker',
                                    'options' => [
                                        'options' => [
                                            'placeholder' => 'Time to...',
                                            'class' => 'col-md-9',
                                            'id' => 'timeEndTeori' . current(explode(' ', $arrMatkul[$i])),
                                            'options' => ['required' => true]
                                        ]]
                                ],
                            ]
                        ],
                        'dosen_matkulList[' . $i . '][]' => [
                            'label' => 'Dosen ' . $arrMatkul[$i],
                            'type' => Form::INPUT_DROPDOWN_LIST,
                            'items' => ArrayHelper::map($dosenAllMatakuliah, 'dosen_id', 'nama_dosen'),
                            'options' => ['id' => 'dosen_matkul_' . $arrMatkul[$i], 'prompt' => 'Pilih Dosen Matakuliah', 'options' => ['required' => true]],
                        ]
                ]]);
                echo "</div>";


                echo "<div id=\"matkulP" . current(explode(' ', $arrMatkul[$i])) . "\" style=\"display:none;\">";



                echo kartik\builder\Form::widget([
                    'model' => $model,
                    'form' => $form,
                    'columns' => 2,
                    'attributes' => [
                        'waktuKuliah' => [
                            'label' => 'Waktu Sesi Praktikum ' . $arrMatkul[$i],
                            'attributes' => [
                                'waktu_mulaiKulList[' . $i . '][]' => [
                                    'type' => Form::INPUT_WIDGET,
                                    'widgetClass' => '\kartik\widgets\TimePicker',
                                    'options' => ['options' => [
                                            'placeholder' => 'Time from...',
                                            'id' => 'timeStartPraktikum' . current(explode(' ', $arrMatkul[$i])),
                                            'options' => ['required' => true]
                                        ]
                                    ],
                                ],
                                'waktu_selesaiKulList[' . $i . '][]' => [
                                    'type' => Form::INPUT_WIDGET,
                                    'widgetClass' => '\kartik\widgets\TimePicker',
                                    'options' => [
                                        'options' => [
                                            'placeholder' => 'Time to...',
                                            'class' => 'col-md-9',
                                            'id' => 'timeEndPraktikum' . current(explode(' ', $arrMatkul[$i])),
                                            'options' => ['required' => true]
                                        ]]
                                ],
                            ]
                        ],
                        'dosen_matkulList[' . $i . '][]' => [
                            'label' => 'Dosen ' . $arrMatkul[$i],
                            'type' => Form::INPUT_DROPDOWN_LIST,
                            'items' => ArrayHelper::map($dosenAllMatakuliah, 'dosen_id', 'nama_dosen'),
                            'options' => ['id' => 'dosen_matkul_' . $arrMatkul[$i], 'prompt' => 'Pilih Dosen Matakuliah', 'options' => ['required' => true]],
                        ]
                ]]);

                echo " </div> </div>";
            }
            ?>

        </div>   
        <br>


    </div>

    <?php
    echo Form::widget([
        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'alasan_ijin' => [
                'label' => 'Alasan',
                'type' => Form::INPUT_TEXTAREA,
                'options' => ['placeholder' => 'Max 160 Characters', 'maxlength' => 160]
            ]
        ]
    ]);

    echo Form::widget(
            [
                'model' => $model,
                'form' => $form,
                'columns' => 1,
                'attributes' => [

                    'lampiran' => [

                        'type' => Form::INPUT_TEXT,
                    ],
                ],
            ]
    );
    ?>


    <div class="form-group">
        <?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
