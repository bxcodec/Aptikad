<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'My Yii Application';
$wali = null;
$dosen = \backend\modules\aitk\models\AitkRDosen::findOne(['account_id' => Yii::$app->user->identity->id]);

if (isset($dosen)) {
    $wali = \backend\modules\aitk\models\AitkRKelas::findOne(['wali' => $dosen->dosen_id]);
}$asrama = \backend\modules\aitk\models\AitkRAsrama::findOne(['account_id' => Yii::$app->user->identity->id]);
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

$link = Url::to('index.php?r=aitk/request/' . $url);
?>


<?php foreach (Yii::$app->session->getAllFlashes() as $message):; ?>
    <?php
    echo \kartik\widgets\Growl::widget([
        'type' => (!empty($message['type'])) ? $message['type'] : 'danger',
        'title' => (!empty($message['title'])) ? Html::encode($message['title']) : 'Title Not Set!',
        'icon' => (!empty($message['icon'])) ? $message['icon'] : 'fa fa-info',
        'body' => (!empty($message['message'])) ? $message['message'] : 'Message Not Set!',
        'showSeparator' => true,
        'delay' => 1, //This delay is how long before the message shows
        'pluginOptions' => [
            'showProgressbar' => true,
            'delay' => (!empty($message['duration'])) ? $message['duration'] : 10000, //This delay is how long the message shows for
            'placement' => [
                'from' => (!empty($message['positonY'])) ? $message['positonY'] : 'top',
                'align' => (!empty($message['positonX'])) ? $message['positonX'] : 'right',
            ]
        ]
    ]);
    ?>
<?php endforeach; ?>

<div class="site-index">

    <div class="jumbotron">
        <h1>APTIKAD</h1>

        <p class="lead">Aplikasi Ijin Tidak Mengikuti Jam Akademik</p>
        <?php
        $akun = \backend\modules\aitk\models\AitkRAccount::findOne(Yii::$app->user->id);
        if ($akun->username == "baakitdel"):
            ?>
            <p><a class="btn btn-lg btn-success" href="<?=Url::to('index.php?r=aitk/request/baak'); ?>">View Summary</a></p>
            <?php
        endif;
        if ($akun->username != "baakitdel"):
            ?>
            <p><a class="btn btn-lg btn-success" href="<?php echo $link ?>">Request</a></p>
        <?php endif; ?>
    </div>



<div class="body-content">

        <div class="row">
            <div class="col-lg-12">
                <h3 class="">Perhatian</h3>

                <p>Hal-hal yang perlu diperhatikan jika hendak membuat izin: </p>
                <ul class="list-group-item-heading">
                    <li class="">
                        Jam Kuliah adalah 08.00- 17.00 WIB untuk hari Senin s/d Kamis dan pukul 08.00 s/d 12.00 WIB untuk hari
                        Jumat.
                    </li>
                    <li class="">
                        Jam Akademik adalah Senin-Jumat dari pukul 08.00-17.00 WIB.
                    </li>
                    <li>
                        Jika Mahasiswa tidak hadir kuliah atau keluar kampus , maka mahasiswa harus meminta izin kepada dosen wali, kemudian kepada 
                        pengurus asrama.
                    </li>
                    <li>
                        Jika mahasiswa sakit dan tidak dapat membuat request izin, dapat di buatkan oleh pihak keasramaan.
                    </li>
                </ul>

                <!--<p><a class="btn btn-default" href="http://www.yiiframework.com/doc/">Yii Documentation &raquo;</a></p>-->
            </div>
                    </div>

    </div>
</div>



