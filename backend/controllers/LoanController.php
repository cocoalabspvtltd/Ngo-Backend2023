<?php

namespace backend\controllers;

use Yii;
use backend\models\Loan;
use backend\models\User;
use backend\models\LoanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use Razorpay\Api\Api;

/**
 * LoanController implements the CRUD actions for Loan model.
 */
class LoanController extends Controller
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
     * Lists all Loan models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LoanSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    

    /**
     * Creates a new Loan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Loan();

        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()){
                $imageUrl = UploadedFile::getInstances($model,'image_url');
                if($imageUrl && !empty($imageUrl)){
                    $imageLocation = Yii::$app->params['upload_path_loan_images'];
                    $modelUser = new User;
                    $saveImage = $modelUser->uploadAndSave($imageUrl,$imageLocation);
                    if($saveImage){
                        $model->image_url = $saveImage;
                    }
                }
                $model->closing_date = date('Y-m-d',strtotime($model->closing_date));
                $model->save(false);
                yii::$app->session->setFlash('success','Loan Created Successfully');
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Loan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldImage = $model->image_url;
        if ($model->load(Yii::$app->request->post())) {
            $imageUrl = UploadedFile::getInstances($model,'image_url');
            if($imageUrl && !empty($imageUrl)){
                $imageLocation = Yii::$app->params['upload_path_loan_images'];
                $modelUser = new User;
                $saveImage = $modelUser->uploadAndSave($imageUrl,$imageLocation);
                if($saveImage){
                    $model->image_url = $saveImage;
                }
            }else{
                $model->image_url = $oldImage;
            }
            $model->closing_date = date('Y-m-d',strtotime($model->closing_date));
            $model->save(false);
            yii::$app->session->setFlash('success','Loan Updated Successfully');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Loan model.
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

        return $this->redirect(['index']);
    }

    /**
     * Finds the Loan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Loan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Loan::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionApprove($id){
        $model = $this->findModel($id);
        $modelUser = User::find()->where(['id'=>$model->created_by])->one();
        $customer_id = $modelUser->customer_id;
        if($customer_id){
            $api_key = KEY_ID;
            $api_secret = KEY_SECRET;
            $api = new Api($api_key, $api_secret);
            $virtualAccountId = $modelUser->virtual_account_id;
            if(!$virtualAccountId){
                $virtualAccount = $api->virtualAccount->create(
                    array(
                        'receiver_types' => array(
                            'bank_account',
                        ),
                        //'customer_id' => $customer_id
                    )
                );
                $modelUser->virtual_account_id = $virtualAccount['id'];
                $modelUser->save(false);
            }
            $virtualAccountId = $modelUser->virtual_account_id;
            $virtualAccount = $api->virtualAccount->fetch($virtualAccountId);
            if($virtualAccount){
                $model->virtual_account_id = $modelUser->virtual_account_id;
                $model->virtual_account_name = $virtualAccount['name'];
                $model->virtual_account_number = $virtualAccount['receivers']['0']['account_number'];
                $model->virtual_account_ifsc = $virtualAccount['receivers']['0']['ifsc'];
                $model->virtual_account_type = 'Current';
                $model->save(false);
            }
        }
        if($model->is_approved == 0){
            $model->is_approved = 1;
            yii::$app->session->setFlash('success','Approved Successfully');
        }else{
            $model->is_approved = 0;
            yii::$app->session->setFlash('success','Rejected Successfully');
        }
        $model->save(false);

        return $this->redirect(['index']);
    }
}
