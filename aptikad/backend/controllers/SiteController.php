<?php

namespace backend\controllers;
use yii\helpers\Html;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\LoginForm;
use yii\filters\VerbFilter;

/**
 * Site controller
 */
class SiteController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex() {


        return $this->render('index');
    }

    public function actionLogin() {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            $request = "";
            $dosen = \backend\modules\aitk\models\AitkRDosen::findOne(['account_id' => Yii::$app->user->id]);
            $asrama = \backend\modules\aitk\models\AitkRAsrama::findOne(['account_id' => Yii::$app->user->id]);


            if (isset($dosen) || isset($asrama)) {
                
                if(isset($dosen))
                $jumlah_request_dosen = \backend\modules\aitk\models\AitkRequest::find()->where(['status_dosen' => NULL, 'dosen_wali' => $dosen->dosen_id])->count();
                $jumlah_request_asrama = \backend\modules\aitk\models\AitkRequest::find()->where(['status_dosen' => 1, 'status_asrama' => NULL])->count();

                $total_request = isset($dosen) ? $jumlah_request_dosen : (isset($asrama) ? $jumlah_request_asrama : 0);
                $url = isset($dosen)?'dosenwali':'asrama';
                if ($total_request > 0) {
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'info',
                        'delay' => 100000,
                        'icon' => 'glyphicon glyphicon-warning-sign',
                        'message' => 'Anda Memiliki <a href='.\yii\helpers\Url::to('index.php?r=aitk/request/'.$url) .'><b>'. $total_request . ' Pending Request</b></a> ',
                        'title' => 'Pending Request',
                    ]);
                }
                
            }
            return $this->redirect(['index']);
            
        } else {
            return $this->render('login', [
                        'model' => $model,
            ]);
        }
    }

    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

}
