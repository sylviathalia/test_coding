<?php
namespace app\controllers;

use Yii;
use app\models\Account;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

class AccountController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index','create','update','delete','view'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule, $action) {
                            return Yii::$app->user->identity->role === 'Admin';
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $accounts = Account::find()->all();
        return $this->render('index', ['accounts' => $accounts]);
    }

    public function actionView($username)
    {
        return $this->render('view', ['model' => $this->findModel($username)]);
    }

    public function actionCreate()
    {
        $model = new Account();
        if ($model->load(Yii::$app->request->post())) {
            // controller expects password plain in field 'passwordPlain'
            $model->passwordPlain = $model->password; // form field named `password`
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($username)
    {
        $model = $this->findModel($username);
        if ($model->load(Yii::$app->request->post())) {
            if (!empty($model->password)) {
                $model->passwordPlain = $model->password;
            } else {
                // keep old password
                $model->password = $model->getOldAttribute('password');
            }
            if ($model->save()) return $this->redirect(['index']);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($username)
    {
        $this->findModel($username)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($username)
    {
        if (($model = Account::findOne($username)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Akun tidak ditemukan.');
    }
}
