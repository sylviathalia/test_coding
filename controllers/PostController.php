<?php
namespace app\controllers;

use Yii;
use app\models\Post;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

class PostController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index','view','create','update','delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index','view'],
                        'roles' => ['?','@'], // semua bisa lihat postingan
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['@'], // login user bisa buat posting
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update','delete'],
                        'roles' => ['@'],
                        'matchCallback' => function($rule, $action) {
                            $id = Yii::$app->request->get('id');
                            $post = Post::findOne($id);
                            if (!$post) return false;
                            $user = Yii::$app->user->identity;
                            // Admin bisa edit semua, Author hanya miliknya
                            if ($user->role === 'Admin') return true;
                            return $post->username === $user->username;
                        }
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $posts = Post::find()->orderBy(['date' => SORT_DESC])->all();
        return $this->render('index', ['posts' => $posts]);
    }

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate()
    {
        $model = new Post();
        if ($model->load(Yii::$app->request->post())) {
            $model->username = Yii::$app->user->identity->username; // ambil username login
            $model->date = date('Y-m-d H:i:s'); // isi tanggal sekarang
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->idpost]);
            }
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->idpost]);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Post tidak ditemukan.');
    }
}
