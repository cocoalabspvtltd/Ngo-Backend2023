<?php

namespace backend\controllers;

use Yii;
use backend\models\Campaign;
use backend\models\User;
use backend\models\CampaignSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * CampaignController implements the CRUD actions for Campaign model.
 */
class CampaignController extends Controller
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
     * Lists all Campaign models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CampaignSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Campaign model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Campaign model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Campaign();

        if ($model->load(Yii::$app->request->post())) {
            $iconUrl = UploadedFile::getInstances($model,'icon_url');
            if($iconUrl){
                $model->icon_url = '1';
            }
            if($model->validate() && $model->icon_url){
                if($iconUrl && !empty($iconUrl)){
                    $imageLocation = Yii::$app->params['upload_path_campaign_icons'];
                    $modelUser = new User;
                    $saveImage = $modelUser->uploadAndSave($iconUrl,$imageLocation);
                    if($saveImage){
                        $model->icon_url = $saveImage;
                    }
                }
                $model->save(false);
                yii::$app->session->setFlash('success','Campaign Created Successfully');
                return $this->redirect(['index']);
            }else{
                $model->addError('icon_url','Icon cannot be blank');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Campaign model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $icon = $model->icon_url;
        if ($model->load(Yii::$app->request->post())) {
            $iconUrl = UploadedFile::getInstances($model,'icon_url');
            if($iconUrl){
                $model->icon_url = '1';
            }else{
                $model->icon_url = $icon;
            }
            if($model->validate()){
                if($iconUrl && !empty($iconUrl)){
                    $imageLocation = Yii::$app->params['upload_path_campaign_icons'];
                    $modelUser = new User;
                    $saveImage = $modelUser->uploadAndSave($iconUrl,$imageLocation);
                    if($saveImage){
                        $model->icon_url = $saveImage;
                    }
                }else{
                    $model->icon_url = $icon;
                }
                $model->save(false);
                yii::$app->session->setFlash('success','Campaign Updated Successfully');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Campaign model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = 0;
        $model->save(false);
        yii::$app->session->setFlash('success','Campaign Deleted Successfully');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Campaign model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Campaign the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Campaign::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
