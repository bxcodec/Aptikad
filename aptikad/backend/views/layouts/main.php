<?php

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <div class="wrap">
            <?php
            NavBar::begin([
                'brandLabel' => 'Aplikasi Izin Tidak Mengikuti Jam Akademik (APTIKAD)',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            $menuItems = [
                ['label' => 'Home', 'url' => ['/site/index']],
];
if (Yii::$app->user->isGuest) {
                $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
            } else {
                $akun = common\models\AitkRAccount::findOne(Yii::$app->user->id);

                if ($akun->username == "baakitdel") {
                    $menuItems[] = ['label' => 'View Summary',
                        'url' => ['/aitk/request/baak']];
                } else {

                    $wali = null;
                    $dosen = \backend\modules\aitk\models\AitkRDosen::findOne(['account_id' => Yii::$app->user->identity->id]);

                    if (isset($dosen)) {
                        $wali = \backend\modules\aitk\models\AitkRKelas::findOne(['wali' => $dosen->dosen_id]);
                    }
                    $asrama = \backend\modules\aitk\models\AitkRAsrama::findOne(['account_id' => Yii::$app->user->identity->id]);
                    $mahasiswa = \backend\modules\aitk\models\AitkRMahasiswa::findOne(['account_id' => Yii::$app->user->identity->id]);

                    $url = "";

                    if (isset($dosen))
                        $url = "dosen";
                    if (isset($wali))
                        $url = "dosenwali";
                    if (isset($asrama))
                        $url = "asrama";
                    if (isset($mahasiswa))
                        $url = "index";

                    $menuItems[] = [
                        'label' => 'Request',
                        'url' => ['/aitk/request/' . $url],
//                    'linkOptions' => ['data-method' => 'post']
                    ];
                }
                $menuItems[] = [
                    'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ];
            }
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
            ]);
            NavBar::end();
            ?>

            <div class="container">
<?=
Breadcrumbs::widget([
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
])
?>
                <?= $content ?>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; SYP_06 TI 2 <?= date('Y') ?></p>
                <p class="pull-right"><?= Yii::powered() ?></p>
            </div>
        </footer>

<?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
