<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\aitk\models\AitkRequest */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="aitk-request-form">

    <?php $form = ActiveForm::begin(); ?>





    <?php

    use vova07\imperavi\Widget;

echo $form->field($model, 'message')->widget(Widget::className(), [
        'settings' => [
            'lang' => 'en',
            'minHeight' => 200,
            'plugins' => [
                'clips',
                'fullscreen',

            ]
        ]
    ]);
    ?>




    <div class="form-group">
<?= Html::submitButton('Send Email', ['class' => 'btn btn-info']) ?>
    </div>





<?php ActiveForm::end(); ?>

</div>
