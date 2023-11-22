<?php

namespace backend\controllers;

use Yii;
use backend\models\Donation;
use backend\models\User;
use backend\models\DonationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\FundraiserScheme;

/**
 * DonationController implements the CRUD actions for Donation model.
 */
class DonationController extends Controller
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
     * Lists all Donation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DonationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionAllDonations()
    {
        // header('Access-Control-Allow-Origin: *');
         
         //$donation_list = Donation::find()->all();
         
         //$fundraiser_id = $donation_list->fundraiser_id;
         
         //$fundraiser_name = FundraiserScheme::find()->where(['id'=>$fundraiser_id])->all();
         
         //$user_name = User::find()->where(['id'=>$donation_list->user_id])->all();
         
         // return $this->render('all-donation-list',[
        //'donation_list'=>$donation_list]);
        
         $searchModel = new DonationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('all-donation-list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
     public function actionReceipt($id)
    {
         header('Access-Control-Allow-Origin: *');
        $form_data = Donation::find()->where(['id'=> $id])->one();
        
        $date = date("d-m-Y", strtotime($form_data->created_at));
        $length = 5;
        $string = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,$length);
        $receipt_number = $string.$id;
        
        $a =rand(1, 999);
        
        return $this->render('receipt',[
            'form_data'=>$form_data, 'date'=>$date,'receipt_number' => $receipt_number,'a'=> $a]);
    }

    protected function findModel($id)
    {
        if (($model = Donation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
