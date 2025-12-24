<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\Apple;
use yii\web\NotFoundHttpException;

class AppleController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'generate'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('id');
            $action = Yii::$app->request->post('action');
            $apple = $this->findModel($id);
            try {
                switch ($action) {
                    case 'fall':
                        $apple->fallToGround();
                        break;
                    case 'eat':
                        $percent = (float)Yii::$app->request->post('percent');
                        $apple->eat($percent);
                        break;
                }
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            // return $this->redirect(['index']);
        }

        return $this->render('index', ['apples' => Apple::find()->all()]);
    }

    public function actionGenerate($count = 1)
    {
        for ($i = 0; $i < $count; $i++) {
            Apple::create();
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Apple::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException("Apple is not found");
    }
}