<?php

namespace backend\controllers;

use Yii;
use backend\models\FundraiserSchemeUpdateRequest;
use backend\models\FundraiserScheme;
use backend\models\FundraiserComment;
use backend\models\User;
use backend\models\FundraiserSchemeUpdateRequestSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * FundraiserSchemeUpdateRequestController implements the CRUD actions for FundraiserSchemeUpdateRequest model.
 */
class FundraiserSchemeUpdateRequestController extends Controller
{
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
                'only' => ['index','approve','reject','view','delete'],
                'rules' => [
                    [
                        'actions' => ['index','approve','reject','delete','view'],
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
     * Lists all FundraiserSchemeUpdateRequest models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FundraiserSchemeUpdateRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FundraiserSchemeUpdateRequest model.
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
     * Finds the FundraiserSchemeUpdateRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FundraiserSchemeUpdateRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FundraiserSchemeUpdateRequest::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    

    public function actionApprove($id){
        $model = $this->findModel($id);
        $modelFundraiser = FundraiserScheme::find()->where(['id'=>$model->fundraiser_id])->one();
        if($model->campaign_id){
            $modelFundraiser->campaign_id = $model->campaign_id;
        }
        if($model->image_url){
            $modelFundraiser->image_url = $model->image_url;
        }
        if($model->title){
            $modelFundraiser->title = $model->title;
        }
        if($model->fund_required){
            $modelFundraiser->fund_required = $model->fund_required;
        }
        if($model->closing_date){
            $modelFundraiser->closing_date = $model->closing_date;
        }
        if($model->story){
            $modelFundraiser->story = $model->story;
        }
        if($model->name){
            $modelFundraiser->name = $model->name;
        }
        if($model->email){
            $modelFundraiser->email = $model->email;
        }
        if($model->phone_number){
            $modelFundraiser->phone_number = $model->phone_number;
        }
        if($model->country_code){
            $modelFundraiser->country_code = $model->country_code;
        }
        if($model->relation_master_id){
            $modelFundraiser->relation_master_id = $model->relation_master_id;
        }
        if($model->patient_name){
            $modelFundraiser->patient_name = $model->patient_name;
        }
        if($model->health_issue){
            $modelFundraiser->health_issue = $model->health_issue;
        }
        if($model->hospital){
            $modelFundraiser->hospital = $model->hospital;
        }
        if($model->city){
            $modelFundraiser->city = $model->city;
        }
        if($model->beneficiary_account_name){
            $modelFundraiser->beneficiary_account_name = $model->beneficiary_account_name;
        }
        if($model->beneficiary_account_number){
            $modelFundraiser->beneficiary_account_number = $model->beneficiary_account_number;
        }
        if($model->beneficiary_bank){
            $modelFundraiser->beneficiary_bank = $model->beneficiary_bank;
        }
        if($model->beneficiary_ifsc){
            $modelFundraiser->beneficiary_ifsc = $model->beneficiary_ifsc;
        }
        if($model->beneficiary_image){
            $modelFundraiser->beneficiary_image = $model->beneficiary_image;
        }
        if($model->pricing_id){
            $modelFundraiser->pricing_id = $model->pricing_id;
        }
        $model->is_approved = 1;
        $model->status = 0;
        $modelFundraiser->save(false);
        $model->save(false);
        yii::$app->session->setFlash('success','Approved Successfully');
        return $this->redirect(['campaign-fundraiser-scheme/index']);
    }

    public function actionReject($id){
        $model = $this->findModel($id);
        $model->is_approved = -1;
        $model->save(false);
        
        yii::$app->session->setFlash('success','Rejected Successfully');
        return $this->redirect(['index']);
    }
    
    public function actionComment($id){
        $modelFundraiserComment = new FundraiserComment;
        $model = $this->findModel($id);
        $modelFundraiser = FundraiserScheme::find()->where(['id'=>$model->fundraiser_id])->one();
        $params = Yii::$app->request->post();
        if($params && $modelFundraiserComment->load($params) && $modelFundraiserComment->validate()){
            $senderId = Yii::$app->user->identity->id;
            $receiverId = $modelFundraiser->created_by;
            $fundraiserId = $modelFundraiser->id;
            $modelFundraiserComment->sender_id = $senderId;
            $modelFundraiserComment->receiver_id = $receiverId;
            $modelFundraiserComment->fundraiser_id = $fundraiserId;
            $modelFundraiserComment->save(false);
            $modelUser = User::find()->where(['id'=>$receiverId])->one();
            Yii::$app->email->sendComment($modelFundraiser,$modelFundraiserComment->message,$modelUser->email,$modelUser->name);
            yii::$app->session->setFlash('success','Commented Successfully');
            return $this->redirect(['index']);
        }
        return $this->render('comment',[
            'modelFundraiserComment' => $modelFundraiserComment,
            'modelFundraiser' => $modelFundraiser,
            'model' => $model
        ]);
    }
}
