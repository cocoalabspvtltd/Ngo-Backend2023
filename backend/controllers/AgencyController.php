<?php

namespace backend\controllers;

use Yii;
use backend\models\Agency;
use backend\models\User;
use backend\models\AgencyDonation;
use backend\models\FundraiserScheme;
use backend\models\AgencySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Razorpay\Api\Api;
use common\models\AgencyLoginForm;
use yii\db\Query;

/**
 * AgencyController implements the CRUD actions for Agency model.
 */
class AgencyController extends Controller
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
     * Lists all Agency models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AgencySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Agency model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
         $password = 'password';
         $password_hash =password_hash($password, PASSWORD_DEFAULT);
         $model = new Agency();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save(false);
            $api_key = KEY_ID;
            $api_secret = KEY_SECRET;
            $api = new Api($api_key, $api_secret);
            $customer = $api->customer->create(
                array(
                    'name' => $model->name,
                    'email' => $model->email
                )
            );
            $model->customer_id = $customer['id'];
            $model->password = 'password';
            $model->password_hash = $password_hash;
            $model->save(false);
            if($model->save(false))
            
             $user= new User();
             $user->username= $model->email ;
             $user->password_hash= $password_hash;
             $user->role= 'agency' ;
             $user->phone_number= $model->phone;
             $user->name= $model->name;
             $user->email= $model->email;
             $user->save(false);
            yii::$app->session->setFlash('success','Agency Created Successfully');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Agency model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save(false);
            $api_key = KEY_ID;
            $api_secret = KEY_SECRET;
            $api = new Api($api_key, $api_secret);
            $customer = $api->customer->fetch($model->customer_id)->edit([
                'name'  => $model->name,
                'email' => $model->email
            ]);
            $model->customer_id = $customer['id'];
            $model->save(false);
            yii::$app->session->setFlash('success','Agency Updated Successfully');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Agency model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        yii::$app->session->setFlash('success','Agency Deleted Successfully');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Agency model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Agency the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Agency::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
     public function actionAgencyLogin()
    {
       
        $this->layout = 'blank';
         
        $model = new AgencyLoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionAgencyProfile()
    {
        $id = Yii::$app->user->id;
        $agency_email = User::find()->where(['id'=>$id])->one();
        $dataProvider = Agency::find()->where(['email'=>$agency_email->username])->one();
        
         return $this->render('agency-profile',[
            'dataProvider' => $dataProvider,]);
    }
    
    public function actionPaymentHistory()
    {

        $id = Yii::$app->user->id;
     
         $query = Agency::find()
    ->leftJoin('user', 'user.username = agency.email')
    ->where(['user.id' =>  $id])->one();
    
    
        $data = AgencyDonation::find()->where(['agency_id'=>$query->id])->one();
        $agency_name = $query->name;
        $fundraiser_scheme = FundraiserScheme::find()->where(['id'=>$data->fundraiser_id])->one();
        
         $datas = AgencyDonation::find()->where(['agency_id'=>$query->id])->all();
      
         return $this->render('payment-histrory',[
            'data' => $datas,'agency_name'=>$agency_name,'fundraiser_name'=>$fundraiser_scheme->title]);
    }

}
