<?php

namespace backend\controllers;

use Yii;
use backend\models\OurTeam;
use backend\models\User;
use backend\models\OurTeamSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * OurTeamController implements the CRUD actions for OurTeam model.
 */
class OurTeamController extends Controller
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
     * Lists all OurTeam models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OurTeamSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates a new OurTeam model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OurTeam();

        if ($model->load(Yii::$app->request->post())) {
            $imageUrl = UploadedFile::getInstances($model,'image_url');
            if($imageUrl){
                $model->image_url = '1';
            }
            if($model->validate() && $model->image_url){
                if($imageUrl && !empty($imageUrl)){
                    $imageLocation = Yii::$app->params['upload_path_profile_images'];
                    $modelUser = new User;
                    $saveImage = $modelUser->uploadAndSave($imageUrl,$imageLocation);
                    if($saveImage){
                        $model->image_url = $saveImage;
                    }
                }
                $model->save(false);
                yii::$app->session->setFlash('success','Created Successfully');
                return $this->redirect(['index']);
            }else{
                $model->addError('image_url','Image cannot be blank');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OurTeam model.
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
                    $imageLocation = Yii::$app->params['upload_path_profile_images'];
                    $modelUser = new User;
                    $saveImage = $modelUser->uploadAndSave($iconUrl,$imageLocation);
                    if($saveImage){
                        $model->image_url = $saveImage;
                    }
                }else{
                    $model->image_url = $icon;
                }
                $model->save(false);
                yii::$app->session->setFlash('success','Updated Successfully');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing OurTeam model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        yii::$app->session->setFlash('success','Deleted Successfully');

        return $this->redirect(['index']);
    }

    /**
     * Finds the OurTeam model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OurTeam the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OurTeam::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
