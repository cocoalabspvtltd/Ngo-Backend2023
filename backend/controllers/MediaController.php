<?php

namespace backend\controllers;

use Yii;
use backend\models\Media;
use backend\models\User;
use backend\models\MediaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * MediaController implements the CRUD actions for Media model.
 */
class MediaController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST','GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','create','update','view','delete'],
                'rules' => [
                    [
                        'actions' => ['index','create','update','delete','view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function($rule, $action) {
                return $this->goHome();
                }
                ]
        ];
    }

    /**
     * Lists all Media models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MediaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates a new Media model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Media();

        if ($model->load(Yii::$app->request->post())) {
            $image_url = UploadedFile::getInstances($model,'image_url');
            if($image_url && !empty($image_url)){
                $imageLocation = Yii::$app->params['upload_path_media_images'];
                $modelUser = new User;
                $saveImage = $modelUser->uploadAndSave($image_url,$imageLocation);
                if($saveImage){
                    $model->image_url = $saveImage;
                }
            }
            $model->save(false);
            yii::$app->session->setFlash('success','Media Created Successfully');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Media model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $icon = $model->image_url;
        if ($model->load(Yii::$app->request->post())) {
            $iconUrl = UploadedFile::getInstances($model,'image_url');
            if($iconUrl){
                $model->image_url = '1';
            }else{
                $model->image_url = $icon;
            }
            if($model->validate()){
                if($iconUrl && !empty($iconUrl)){
                    $imageLocation = Yii::$app->params['upload_path_media_images'];
                    $modelUser = new User;
                    $saveImage = $modelUser->uploadAndSave($iconUrl,$imageLocation);
                    if($saveImage){
                        $model->image_url = $saveImage;
                    }
                }else{
                    $model->image_url = $icon;
                }
                $model->save(false);
                yii::$app->session->setFlash('success','Media Updated Successfully');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Media model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Media model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Media the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Media::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
