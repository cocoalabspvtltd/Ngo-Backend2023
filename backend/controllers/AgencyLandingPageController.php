<?php

namespace backend\controllers;

use Yii;
use backend\models\AgencyLandingPage;
use backend\models\Agency;
use backend\models\AgencyLandingPageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Razorpay\Api\Api;

/**
 * AgencyLandingPageController implements the CRUD actions for AgencyLandingPage model.
 */
class AgencyLandingPageController extends Controller
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
     * Lists all AgencyLandingPage models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AgencyLandingPageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AgencyLandingPage model.
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
     * Creates a new AgencyLandingPage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AgencyLandingPage();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save(false);
            $url = "https://crowdworksindia.org/ngo-landing-page/ngo/#/campaignDetail/".$model->id;
            $model->landing_page_url = $url;
            $modelAgency = Agency::find()->where(['status'=>1,'id'=>$model->agency_id])->one();
            $customer_id = $modelAgency->customer_id;
            if(!$customer_id){
                $api_key = KEY_ID;
                $api_secret = KEY_SECRET;
                $api = new Api($api_key, $api_secret);
                $customer = $api->customer->create(
                    array(
                        'name' => $modelAgency->name,
                        'email' => $modelAgency->email
                    )
                );
                $modelAgency->customer_id = $customer['id'];
                $modelAgency->save(false);
                $customer_id = $modelAgency->customer_id;
            }
            $api_key = KEY_ID;
            $api_secret = KEY_SECRET;
            $api = new Api($api_key, $api_secret);
            $virtualAccount = $api->virtualAccount->create(
                array(
                    'receiver_types' => array(
                        'bank_account'
                    ),
                    //'customer_id' => $customer_id
                )
            );
            $model->virtual_account_id = $virtualAccount['id'];
            
            $virtualAccount = $api->virtualAccount->fetch($virtualAccount['id']);
            $model->virtual_account_name = $virtualAccount['name'];
            $model->virtual_account_number = $virtualAccount['receivers']['0']['account_number'];
            $model->virtual_account_ifsc = $virtualAccount['receivers']['0']['ifsc'];
            $model->virtual_account_type = 'Current';
            $model->save(false);
            yii::$app->session->setFlash('success','Agency Landing Page Created Successfully');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AgencyLandingPage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            yii::$app->session->setFlash('success','Agency Landing Page Updated Successfully');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AgencyLandingPage model.
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
     * Finds the AgencyLandingPage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AgencyLandingPage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AgencyLandingPage::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
}
