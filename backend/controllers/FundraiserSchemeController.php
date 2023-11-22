<?php

namespace backend\controllers;

use Yii;
use backend\models\FundraiserScheme;
use backend\models\FundraiserSchemeDocuments;
use backend\models\User;
use backend\models\Campaign;
use backend\models\FundraiserSchemeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use Razorpay\Api\Api;
use api\modules\v1\models\Account;

/**
 * FundraiserSchemeController implements the CRUD actions for FundraiserScheme model.
 */
class FundraiserSchemeController extends Controller
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
                'only' => ['index','create','update','view','delete','delete-image'],
                'rules' => [
                    [
                        'actions' => ['index','create','update','delete','view','delete-image'],
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
     * Lists all FundraiserScheme models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FundraiserSchemeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FundraiserScheme model.
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
     * Creates a new FundraiserScheme model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        
       $model = new FundraiserScheme();
        $userId = Yii::$app->user->identity->id;
        $modelUser = User::find()->where(['id'=>$userId])->one();
        $customer_id = $modelUser->customer_id;
        if(!$customer_id){
            $api_key = KEY_ID;
            $api_secret = KEY_SECRET;
            $api = new Api($api_key, $api_secret);
            $customer = $api->customer->create(
                array(
                    'name' => $modelUser->name,
                    'email' => $modelUser->email
                )
            );
            $modelUser->customer_id = $customer['id'];
            $modelUser->save(false);
        }

        if ($model->load(Yii::$app->request->post())) {
            $imageUrl = UploadedFile::getInstances($model,'image_url');
            $beneficiary_image = UploadedFile::getInstances($model,'beneficiary_image');
            $documents = UploadedFile::getInstances($model,'documents');
            if($imageUrl){
                $model->image_url = '1';
            }
            if($model->validate() && $model->image_url){
                if($imageUrl && !empty($imageUrl)){
                    $imageLocation = Yii::$app->params['upload_path_fundraiser_images'];
                    $modelUser = new User;
                    $saveImage = $modelUser->uploadAndSave($imageUrl,$imageLocation);
                    if($saveImage){
                        $model->image_url = $saveImage;
                    }
                } 
                if($beneficiary_image && !empty($beneficiary_image)){
                    $imageLocation = Yii::$app->params['upload_path_fundraiser_images'];
                    $modelUser = new User;
                    $saveImage1 = $modelUser->uploadAndSave($beneficiary_image,$imageLocation);
                    if($saveImage1){
                        $model->beneficiary_image = $saveImage1;
                    }
                } 
                // if($documents){
                //     foreach($documents as $document){
                //         if($document && !empty($document)){
                //             $imageLocation = Yii::$app->params['upload_path_fundraiser_documents'];
                //             $modelUser = new User;
                //             $saveImage2 = $modelUser->uploadAndSave($document,$imageLocation);
                //             if($saveImage2){
                //                 $modelFundraiserDocument = new FundraiserSchemeDocuments;
                //                 $modelFundraiserDocument->fundraiser_scheme_id = $model->id;
                //                 $modelFundraiserDocument->doc_url = $saveImage2;
                //                 $modelFundraiserDocument->save(false);
                //             }
                //         }
                //     }
                // }
                $removedFilesString = (Yii::$app->request->post()['removedFiles'])?Yii::$app->request->post()['removedFiles']:'';
                $removedArray = '';
                if($removedFilesString){
                    $removedArray = explode(',',$removedFilesString);
                }
                if($documents){
                    foreach($documents as $document){
                        if($document && !empty($document)){
                            $name = $document->name;
                            if($removedArray){
                                if(in_array($name,$removedArray)){
                                }else{
                                    $imageLocation = Yii::$app->params['upload_path_fundraiser_documents'];
                                    $modelUser = new User;
                                    $saveImage2 = $modelUser->uploadAndSave($document,$imageLocation);
                                    if($saveImage2){
                                        $modelFundraiserDocument = new FundraiserSchemeDocuments;
                                        $modelFundraiserDocument->fundraiser_scheme_id = $model->id;
                                        $modelFundraiserDocument->doc_url = $saveImage2;
                                        $modelFundraiserDocument->save(false);
                                    }
                                }
                            }else{
                                $imageLocation = Yii::$app->params['upload_path_fundraiser_documents'];
                                $modelUser = new User;
                                $saveImage2 = $modelUser->uploadAndSave($document,$imageLocation);
                                if($saveImage2){
                                    $modelFundraiserDocument = new FundraiserSchemeDocuments;
                                    $modelFundraiserDocument->fundraiser_scheme_id = $model->id;
                                    $modelFundraiserDocument->doc_url = $saveImage2;
                                    $modelFundraiserDocument->save(false);
                                }
                            }
                        }
                    }
                }
                $model->created_by = Yii::$app->user->identity->id;
                $model->closing_date = date('Y-m-d',strtotime($model->closing_date));
                $model->is_approved = 1;
                $model->save(false);

                if($customer_id){
                    $api_key = KEY_ID;
                    $api_secret = KEY_SECRET;
                    $api = new Api($api_key, $api_secret);
                    $virtualAccount = $api->virtualAccount->create(
                        array(
                            'receiver_types' => array(
                                'bank_account'
                            ),
                           // 'customer_id' => $customer_id
                        )
                    );
                    $model->virtual_account_id = $virtualAccount['id'];
                    
                    $virtualAccount = $api->virtualAccount->fetch($virtualAccount['id']);
                    $model->virtual_account_name = $virtualAccount['name'];
                    $model->virtual_account_number = $virtualAccount['receivers']['0']['account_number'];
                    $model->virtual_account_ifsc = $virtualAccount['receivers']['0']['ifsc'];
                    $model->virtual_account_type = 'Current';
                    $model->is_campaign = 1;
                    $model->save(false);
                }

                yii::$app->session->setFlash('success','Campaign Created Successfully');
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
     * Updates an existing FundraiserScheme model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $userId = Yii::$app->user->identity->id;
        $modelUser = User::find()->where(['id'=>$userId])->one();
        $customer_id = $modelUser->customer_id;
        $image = $model->image_url;
        $beneficiaryImage = $model->beneficiary_image;
         

        if ($model->load(Yii::$app->request->post())) {
            $removedFilesString = (Yii::$app->request->post()['removedFiles'])?Yii::$app->request->post()['removedFiles']:'';
            $removedArray = '';
            if($removedFilesString){
                $removedArray = explode(',',$removedFilesString);
            }
            $imageUrl = UploadedFile::getInstances($model,'image_url');
            $beneficiary_image = UploadedFile::getInstances($model,'beneficiary_image');
            $documents = UploadedFile::getInstances($model,'documents');

            if($imageUrl){
                $model->image_url = '1';
            }
            if($model->validate()){
                if($documents){
                    foreach($documents as $document){
                        if($document && !empty($document)){
                            $name = $document->name;
                            if($removedArray){
                                if(in_array($name,$removedArray)){
                                }else{
                                    $imageLocation = Yii::$app->params['upload_path_fundraiser_documents'];
                                    $modelUser = new User;
                                    $saveImage2 = $modelUser->uploadAndSave($document,$imageLocation);
                                    if($saveImage2){
                                        $modelFundraiserDocument = new FundraiserSchemeDocuments;
                                        $modelFundraiserDocument->fundraiser_scheme_id = $model->id;
                                        $modelFundraiserDocument->doc_url = $saveImage2;
                                        $modelFundraiserDocument->save(false);
                                    }
                                }
                            }else{
                                $imageLocation = Yii::$app->params['upload_path_fundraiser_documents'];
                                $modelUser = new User;
                                $saveImage2 = $modelUser->uploadAndSave($document,$imageLocation);
                                if($saveImage2){
                                    $modelFundraiserDocument = new FundraiserSchemeDocuments;
                                    $modelFundraiserDocument->fundraiser_scheme_id = $model->id;
                                    $modelFundraiserDocument->doc_url = $saveImage2;
                                    $modelFundraiserDocument->save(false);
                                }
                            }
                        }
                    }
                }
                if($imageUrl && !empty($imageUrl)){
                   
                    $imageLocation = Yii::$app->params['upload_path_fundraiser_images'];
                    $modelUser = new User;
                    $saveImage = $modelUser->uploadAndSave($imageUrl,$imageLocation);
                    if($saveImage){
                        $model->image_url = $saveImage;
                    }
                }else{
                    $model->image_url = $image;
                } 
                if($beneficiary_image && !empty($beneficiary_image)){
                
                    $imageLocation = Yii::$app->params['upload_path_fundraiser_images'];
                    $modelUser = new User;
                    $saveImage1 = $modelUser->uploadAndSave($beneficiary_image,$imageLocation);
                    if($saveImage1){
                        $model->beneficiary_image = $saveImage1;
                    }
                }else{
                    $model->beneficiary_image = $beneficiaryImage;
                } 
               
                $model->closing_date = date('Y-m-d',strtotime($model->closing_date));
                $model->is_campaign = 1;
                $model->save(false);

                if($customer_id){
                    $api_key = KEY_ID;
                    $api_secret = KEY_SECRET;
                    $api = new Api($api_key, $api_secret);
                    $virtualAccountId = $model->virtual_account_id;
                    if(!$virtualAccountId){
                        $virtualAccount = $api->virtualAccount->create(
                            array(
                                'receiver_types' => array(
                                    'bank_account'
                                ),
                                'customer_id' => $customer_id
                            )
                        );
                        $model->virtual_account_id = $virtualAccount['id'];
                        $model->save(false);
                    }
                    $virtualAccountId = $model->virtual_account_id;
                    $virtualAccount = $api->virtualAccount->fetch($virtualAccountId);
                    if($virtualAccount){
                        $model->virtual_account_name = $virtualAccount['name'];
                        $model->virtual_account_number = $virtualAccount['receivers']['0']['account_number'];
                        $model->virtual_account_ifsc = $virtualAccount['receivers']['0']['ifsc'];
                        $model->virtual_account_type = 'Current';
                        $model->save(false);
                    }
                }

                yii::$app->session->setFlash('success','Campaign Updated Successfully');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing FundraiserScheme model.
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
        yii::$app->session->setFlash('success','Fundraiser Sheme Deleted Successfully');

        return $this->redirect(['index']);
    }

    /**
     * Finds the FundraiserScheme model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FundraiserScheme the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FundraiserScheme::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionDeleteImage($id){
        $modelFundraiserDocument = FundraiserSchemeDocuments::find()->where(['id'=>$id])->one();
        if($modelFundraiserDocument){
            $modelFundraiserDocument->status = 0;
            $modelFundraiserDocument->save(false);
        }
        yii::$app->session->setFlash('success','Document deleted Successfully');
        return $this->redirect(['view','id'=>$modelFundraiserDocument->fundraiser_scheme_id]);
    }
    public function actionGetCampaign(){
        $cam = $_POST['cam'];
        $campaign = Campaign::find()->where(['id'=>$cam])->one();
        return $campaign->is_health_case;
    }
    public function actionRemoveImage(){
        $id = $_POST['id'];
        $modelFundraiserDocument = FundraiserSchemeDocuments::find()->where(['id'=>$id])->one();
        if($modelFundraiserDocument){
            $modelFundraiserDocument->status = 0;
            $modelFundraiserDocument->save(false);
        }
        return 'success';
    }
    
    public function actionReject($id)
    {
        
        $model = $this->findModel($id);
        $model->is_approved = -1;
        yii::$app->session->setFlash('success','Cancelled Successfully');
        $model->save(false);

        Yii::$app->email->sendCampaignCancelled($model);
        $title = "Campaign Cancelled";
        $value = $model->created_by;
        $type = 'Campaign';
        $typeVal = $model->id;
        Yii::$app->notification->sendToOneUser($title,$value,$type,$typeVal);
        $country_code = $model->country_code;
        $phone_number = $model->phone_number;
        $sendSms = (new Account)->sendSMS(SMS_KEY, SMS_USERID, $country_code, $phone_number,$title);

        return $this->redirect(['index']);
    }
    
        public function actionApprove($id){
        $model = $this->findModel($id);
        $model->is_approved = 1;
        yii::$app->session->setFlash('success','Approved Successfully');
        $model->save(false);

        Yii::$app->email->sendFundraiserApprove($model);
        $title = "Great news,".$model->name."! Your fundraiser on the Crowd Works India Foundation Fundraising Platform is now live. Share your cause and inspire support.";
        $value = $model->created_by;
        $type = 'fundraiser';
        $typeVal = $model->id;
        Yii::$app->notification->sendToOneUser($title,$value,$type,$typeVal);
        $country_code = $model->country_code;
        $phone_number = $model->phone_number;
        $sendSms = (new Account)->sendSMS(SMS_KEY, SMS_USERID, $country_code, $phone_number,$title);
        
        return $this->redirect(['index']);
    }
}
