<?php

namespace api\modules\v1\controllers;
use yii;
use yii\rest\ActiveController;
use backend\models\FundraiserScheme;
use backend\models\FundraiserDates;
use backend\models\FundraiserSchemeDocuments;
use backend\models\Donation;
use backend\models\Certificate;
use backend\models\FundraiserSchemeUpdateRequest;
use backend\models\User;
use backend\models\Campaign;
use backend\models\Transaction;
use backend\models\DeductedAmount;
use yii\data\ActiveDataProvider;
use api\modules\v1\models\Account;
use yii\web\UploadedFile;
use Razorpay\Api\Api;
use backend\models\Point;
use backend\models\Subscription;
use common\components\PaytmChecksums;
use backend\models\VanAccount;
use backend\models\AgencyLandingPage;
/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class FundraiserSchemeController extends ActiveController
{
    public $modelClass = 'backend\models\FundraiserScheme';      
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }  
    public function  actionList(){
        header('Access-Control-Allow-Origin: *');
        $baseUrl = Yii::$app->params['base_path_fundraiser_images'];
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        $keyword = isset($get['keyword'])?$get['keyword']:'';
        $category_id = isset($get['category_id'])?$get['category_id']:'';
        $amount = isset($get['amount'])?$get['amount']:'';
        $date = date('Y-m-d');
        $query = FundraiserScheme::find()
        ->leftJoin('user','user.id=fundraiser_scheme.created_by')
        ->where(['fundraiser_scheme.status'=>1,'is_approved'=>1])
        ->andWhere(['>=','closing_date',$date])
        ->andWhere(['user.role'=>'campaigner']);
        //print_r($query);exit;
        //->andWhere(['is_amount_collected'=>1]);
        if($keyword){
            $query->andWhere(['like','title',$keyword]);
        }
        if($category_id){
            $query->andWhere(['campaign_id'=>$category_id]);
        }
        if($amount){
            if($amount == 'asc'){
                $query->orderBy(['fund_required'=>SORT_ASC]);
            }
            if($amount == 'desc'){
                $query->orderBy(['fund_required'=>SORT_DESC]);
            }
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [$page, $per_page]
            ]
        ]);
        $hasNextPage = false;
        if(($page*$per_page) < ($query->count())){
            $hasNextPage = true;
        }
        $webBaseUrl = 'https://www.cocoalabs.in/ngo-web/ngo/fund-raiser-detail/';
        $ret = [
            'statusCode' => 200,
            'baseUrl' => $baseUrl,
            'webBaseUrl' => $webBaseUrl,
            'list' => $dataProvider,
            'page' => (int) $page,
            'perPage' => (int) $per_page,
            'hasNextPage' => $hasNextPage,
            'totalCount' => (int) $query->count(),
            'message' => 'Listed Successfully',
            'success' => true
        ];
        return $ret;
    }
    
    public function actionUserSchemaList()
    {
         header('Access-Control-Allow-Origin: *');
        $baseUrl = Yii::$app->params['base_path_fundraiser_images'];
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        $headers = getallheaders();
        $api_token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');
        if(!$api_token){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $token = (new Account)->getBearerToken($api_token);
        $validateToken = (new Account)->validateToken($token);
        if(!$validateToken){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $user_details = (new Account)->getCusomerDetailsByAPI($token);
        if(!$user_details){
            Yii::$app->response->statusCode = 401;
            $msg = "Somthing went wrong.";
            $ret = [
                'message' => $msg,
                'statusCode' => 401,
                'success' => $success
            ];
            return $ret;
        }
        $userId = $user_details->id;
        
        $query = FundraiserScheme::find()
        ->leftJoin('user','user.id=fundraiser_scheme.created_by')
        ->where(['fundraiser_scheme.created_by'=>$userId])
        ->andWhere(['fundraiser_scheme.fund_transfered'=>1]);
        //->andWhere(['is_amount_collected'=>1]);
        
         $webBaseUrl = 'https://www.cocoalabs.in/ngo-web/ngo/fund-raiser-detail/';
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [$page, $per_page]
            ]
        ]);
        
        $ret = [
            'statusCode' => 200,
            'baseUrl' => $baseUrl,
            'webBaseUrl' => $webBaseUrl,
            'list' => $dataProvider,
            'page' => (int) $page,
            'perPage' => (int) $per_page,
            'totalCount' => (int) $query->count(),
            'message' => 'Listed Successfully',
            'success' => true
        ];
        
        return $ret;
    }
    
        public function  actionCampaignsList(){
            
        header('Access-Control-Allow-Origin: *');
        $baseUrl = Yii::$app->params['base_path_fundraiser_images'];
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        $keyword = isset($get['keyword'])?$get['keyword']:'';
        $category_id = isset($get['category_id'])?$get['category_id']:'';
        $amount = isset($get['amount'])?$get['amount']:'';
        $query = FundraiserScheme::find()
        ->leftJoin('user','user.id=fundraiser_scheme.created_by')
        ->where(['fundraiser_scheme.status'=>1])
        ->andWhere(['fundraiser_scheme.is_approved'=>1])
        ->andWhere(['user.role'=>'super-admin'])->orderBy([
        'id' => SORT_DESC]);
        if($keyword){
            $query->andWhere(['like','title',$keyword]);
        }
        if($category_id){
            $query->andWhere(['campaign_id'=>$category_id]);
        }
        if($amount){
            if($amount == 'asc'){
                $query->orderBy(['fund_required'=>SORT_ASC]);
            }
            if($amount == 'desc'){
                $query->orderBy(['fund_required'=>SORT_DESC]);
            }
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [$page, $per_page]
            ]
        ]);
        $hasNextPage = false;
        if(($page*$per_page) < ($query->count())){
            $hasNextPage = true;
        }
        $webBaseUrl = 'https://www.cocoalabs.in/ngo-web/ngo/fund-raiser-detail/';
        $ret = [
            'statusCode' => 200,
            'baseUrl' => $baseUrl,
            'webBaseUrl' => $webBaseUrl,
            'list' => $dataProvider,
            'page' => (int) $page,
            'perPage' => (int) $per_page,
            'hasNextPage' => $hasNextPage,
            'totalCount' => (int) $query->count(),
            'message' => 'Listed Successfully',
            'success' => true
        ];
        return $ret;
    }
    public function actionDonate(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $post = Yii::$app->request->post();
        $headers = getallheaders();
        $api_token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');
        if(!$api_token){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $token = (new Account)->getBearerToken($api_token);
        $validateToken = (new Account)->validateToken($token);
        if(!$validateToken){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $user_details = (new Account)->getCusomerDetailsByAPI($token);
        if(!$user_details){
            Yii::$app->response->statusCode = 401;
            $msg = "Somthing went wrong.";
            $ret = [
                'message' => $msg,
                'statusCode' => 401,
                'success' => $success
            ];
            return $ret;
        }
        $userId = $user_details->id;
        $fundraiser_id = isset($post['fundraiser_id'])?$post['fundraiser_id']:'';
        $amount = isset($post['amount'])?$post['amount']:'';
        $name = isset($post['name'])?$post['name']:'';
        $email = isset($post['email'])?$post['email']:'';
        $show_donor_information = isset($post['show_donor_information'])?$post['show_donor_information']:0;
        $transaction_id = isset($post['transaction_id'])?$post['transaction_id']:'';

        $certificate_name = isset($post['certificate_name'])?$post['certificate_name']:'';
        $certificate_address = isset($post['certificate_address'])?$post['certificate_address']:'';
        $certificate_phone = isset($post['certificate_phone'])?$post['certificate_phone']:'';
        $certificate_pan = isset($post['certificate_pan'])?$post['certificate_pan']:'';
        
        $modelFundraiser = FundraiserScheme::find()->where(['id'=>$fundraiser_id])->one();
        if(!$modelFundraiser){
            $msg = "Invalid fundraiser id.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
         
        if($fundraiser_id && $amount && $name && $email){
            $modelDonation = new Donation;
            $modelDonation->fundraiser_id = $fundraiser_id;
            $modelDonation->user_id = $userId;
            $modelDonation->name = $name;
            $modelDonation->email = $email;
            $modelDonation->amount = $amount;
            $modelDonation->show_donor_information = $show_donor_information;
            $modelDonation->transaction_id = $transaction_id;
            $modelDonation->donated_by = $userId;
            $modelDonation->save(false);
            
             $donation_id =  $modelDonation->id;
            
            if($modelDonation->save())
            {
                
            $modelTransaction = new Transaction;
            $modelTransaction->donor_name = $name;
            $modelTransaction->donor_email = $email;
            $modelTransaction->fundraiser_id = $fundraiser_id;
            $modelTransaction->payment_id = $transaction_id;
            $modelTransaction->amount = $amount;
            $modelTransaction->save(false);
            
             
            //   if($modelTransaction->save()){

            //   $query = FundraiserScheme::find()
            //   ->where(['id'=>$fundraiser_id])->one();

            //   $query->fund_raised +=$amount;
            //   $query->save(false);

            //   $goal_amt=$query->fund_raised;
            //   $fund_raised= $query->fund_raised;

            //  $reduced_amt= $goal_amt - $fund_raised;
             
            //   }
              
              $modelPoint = Point::find()->where(['status'=>1,'title'=>'donate'])->one();
            if($modelPoint && $modelPoint->point){
                $point = $modelPoint->point;
                $user_details->points = $user_details->points + $point;
                $user_details->save(false);
            }
            }
            if($certificate_name && $certificate_address && $certificate_phone && $certificate_pan){
                //echo "ahsdgsj";
                $modelCertificate = new Certificate;
                $modelCertificate->name = $certificate_name;
                $modelCertificate->address = $certificate_address;
                $modelCertificate->phone_number = $certificate_phone;
                $modelCertificate->pan_number = $certificate_pan;
                $modelCertificate->user_id = $userId;
                $modelCertificate->fundraiser_id = $fundraiser_id;
                $modelCertificate->amount = $amount;
                $modelCertificate->donation_id = $donation_id;
                $modelCertificate->save(false);
            }

            

            $totalAmountCollected = Donation::find()->where(['status'=>1,'fundraiser_id'=>$fundraiser_id])->sum('amount');
            $requiredAmount = $modelFundraiser->fund_required;
            $fundRaised = $modelFundraiser->fund_raised;
            $modelFundraiser->fund_raised = $fundRaised + $amount;
            if($totalAmountCollected >= $requiredAmount){
                $modelFundraiser->is_amount_collected = 1;
            }
            $modelFundraiser->save(false);
            $success = true;
            $msg = "Donation received successfully";
        }else{
            $msg = 'Fundraiser, Amount, Name, Email cannot be blank';
        }
           
        $ret = [
            'statusCode' => $statusCode,
            'success' => $success,
            'message' => $msg
        ];
        return $ret;
    }
    public function actionDonationList(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        $headers = getallheaders();
        $api_token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');
        if(!$api_token){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $token = (new Account)->getBearerToken($api_token);
        $validateToken = (new Account)->validateToken($token);
        if(!$validateToken){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $user_details = (new Account)->getCusomerDetailsByAPI($token);
        if(!$user_details){
            Yii::$app->response->statusCode = 401;
            $msg = "Somthing went wrong.";
            $ret = [
                'message' => $msg,
                'statusCode' => 401,
                'success' => $success
            ];
            return $ret;
        }
        // $query = FundraiserScheme::find()
        // ->leftJoin('donation','donation.fundraiser_id=fundraiser_scheme.id')
        // ->where(['donation.status'=>1,'user_id'=>$user_details->id])
        // ->select('fundraiser_scheme.title,fundraiser_scheme.id,fundraiser_scheme.image_url,donation.amount');
        $query = Donation::find()
        // ->leftJoin('fundraiser_scheme','fundraiser_scheme.id=donation.fundraiser_id')
        ->where(['donation.status'=>1,'user_id'=>$user_details->id])
        ->select('donation.amount,fundraiser_id,id,donated_by');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [$page, $per_page]
            ]
        ]);
        $list = [];
        if($dataProvider->getModels()){
            foreach($dataProvider->getModels() as $fundraiserModel){
                $modelFundraiser = FundraiserScheme::find()->where(['id'=>$fundraiserModel->fundraiser_id])->one();
                $subscribed = false;
                $subscribeId = null;
                $user_id = $user_details->id;
                $fundraiser_id = $modelFundraiser->id;
                $donated_by = $fundraiserModel->donated_by;
                $modelSubscription = Subscription::find()->where(['user_id'=>$user_id,'fundraiser_id'=>$fundraiser_id,'status'=>1,'donation_id'=>$donated_by])->one();
                if($modelSubscription)
                {
                    $subscribed = true;
                    $subscribeId = $modelSubscription->id;
                }
                $list[] = [
                    'title' => ($modelFundraiser->title)?$modelFundraiser->title:'Crowd Works India Foundation',
                    'image_url' => $modelFundraiser->image_url,
                    'amount' => (string) $fundraiserModel->amount,
                    'subscribed' => $subscribed,
                    'subscribe_id' => $subscribeId,
                    'id' => ($modelFundraiser->id)?$modelFundraiser->id:null
                ];
            }
        }
        $hasNextPage = false;
        if(($page*$per_page) < ($query->count())){
            $hasNextPage = true;
        }
        $baseUrl = Yii::$app->params['base_path_fundraiser_images'];
        $ret = [
            'statusCode' => 200,
            'baseUrl' => $baseUrl,
            'list' => $list,
            'page' => (int) $page,
            'perPage' => (int) $per_page,
            'hasNextPage' => $hasNextPage,
            'totalCount' => (int) $query->count(),
            'message' => 'Listed Successfully',
            'success' => true
        ];
        return $ret;
    }
    public function actionTopDonors(){
        header('Access-Control-Allow-Origin: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        $fundraiser_id = isset($get['fundraiser_id'])?$get['fundraiser_id']:20;
        if(!$fundraiser_id){
            $msg = "Fundraiser cannot be blank";
            $ret = [
                'statusCode' => $statusCode,
                'success' =>$success,
                'message' => $msg
            ];
            return $ret;
        }
        $query = Donation::find()
        ->leftJoin('user','user.id=donation.user_id')
        ->where(['donation.status'=>1])
        ->andWhere(['fundraiser_id'=>$fundraiser_id])
        ->select('donation.*,user.image_url');
        $query->orderBy(['amount'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [$page, $per_page]
            ]
        ]);
        $list = [];
        if($dataProvider->getModels()){
            foreach($dataProvider->getModels() as $donationModel){
                $list[] = [
                    'name' => $donationModel->name,
                    'image_url' => $donationModel->image_url,
                    'amount' => (int) $donationModel->amount,
                    'show_donor_information' => (int) $donationModel->show_donor_information
                ];
            }
        }
        $hasNextPage = false;
        if(($page*$per_page) < ($query->count())){
            $hasNextPage = true;
        }
        $totalCollectedAmount = $query->sum('amount');
        $totlaRequiredAmount = FundraiserScheme::find()->where(['id'=>$fundraiser_id])->one()->fund_required;
        $baseUrl = Yii::$app->params['base_path_profile_images'];
        $ret = [
            'statusCode' => 200,
            'baseUrl' => $baseUrl,
            'list' => $list,
            'page' => (int) $page,
            'perPage' => (int) $per_page,
            'hasNextPage' => $hasNextPage,
            'totalCount' => (int) $query->count(),
            'totalCollectedAmount' => (double) $totalCollectedAmount,
            'totlaRequiredAmount' => $totlaRequiredAmount,
            'message' => 'Listed Successfully',
            'success' => true
        ];
        return $ret;
    }
    public function actionSupporters(){
        header('Access-Control-Allow-Origin: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        $fundraiser_id = isset($get['fundraiser_id'])?$get['fundraiser_id']:20;
        if(!$fundraiser_id){
            $msg = "Fundraiser cannot be blank";
            $ret = [
                'statusCode' => $statusCode,
                'success' =>$success,
                'message' => $msg
            ];
            return $ret;
        }
        $query = Donation::find()
        ->leftJoin('user','user.id=donation.user_id')
        ->where(['donation.status'=>1])
        ->andWhere(['fundraiser_id'=>$fundraiser_id])
        ->select('donation.*,user.image_url');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [$page, $per_page]
            ]
        ]);
        $list = [];
        if($dataProvider->getModels()){
            foreach($dataProvider->getModels() as $donationModel){
                $list[] = [
                    'name' => $donationModel->name,
                    'image_url' => $donationModel->image_url,
                    'amount' => (int) $donationModel->amount,
                    'show_donor_information' => (int) $donationModel->show_donor_information
                ];
            }
        }
        $hasNextPage = false;
        if(($page*$per_page) < ($query->count())){
            $hasNextPage = true;
        }
        $baseUrl = Yii::$app->params['base_path_profile_images'];
        $ret = [
            'statusCode' => 200,
            'baseUrl' => $baseUrl,
            'list' => $list,
            'page' => (int) $page,
            'perPage' => (int) $per_page,
            'hasNextPage' => $hasNextPage,
            'totalCount' => (int) $query->count(),
            'message' => 'Listed Successfully',
            'success' => true
        ];
        return $ret;
    }
    public function actionStartFundraiser(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $post = Yii::$app->request->post();
        $headers = getallheaders();
        $api_token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');
        if(!$api_token){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $token = (new Account)->getBearerToken($api_token);
        $validateToken = (new Account)->validateToken($token);
        if(!$validateToken){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $user_details = (new Account)->getCusomerDetailsByAPI($token);
        
        if(!$user_details){
            Yii::$app->response->statusCode = 401;
            $msg = "Somthing went wrong.";
            $ret = [
                'message' => $msg,
                'statusCode' => 401,
                'success' => $success
            ];
            return $ret;
        }
        $userId = $user_details->id;
        $campaign_id = isset($post['campaign_id'])?$post['campaign_id']:'';
        $name = isset($post['name'])?$post['name']:'';
        $email = isset($post['email'])?$post['email']:'';
        $phone_number = isset($post['phone_number'])?$post['phone_number']:'';
        $country_code = isset($post['country_code'])?$post['country_code']:'';
        $relation_master_id = isset($post['relation_master_id'])?$post['relation_master_id']:'';
        $patient_name = isset($post['patient_name'])?$post['patient_name']:'';
        $health_issue = isset($post['health_issue'])?$post['health_issue']:'';
        $hospital = isset($post['hospital'])?$post['hospital']:'';
        $city = isset($post['city'])?$post['city']:'';
        $beneficiary_account_name = isset($post['beneficiary_account_name'])?$post['beneficiary_account_name']:'';
        $beneficiary_account_number = isset($post['beneficiary_account_number'])?$post['beneficiary_account_number']:'';
        $beneficiary_bank = isset($post['beneficiary_bank'])?$post['beneficiary_bank']:'';
        $beneficiary_ifsc = isset($post['beneficiary_ifsc'])?$post['beneficiary_ifsc']:'';
        $fund_required = isset($post['fund_required'])?$post['fund_required']:'';
        $title = isset($post['title'])?$post['title']:'';
        $no_of_days = isset($post['no_of_days'])?$post['no_of_days']:'';
        $story = isset($post['story'])?$post['story']:'';
        $pricing_id = isset($post['pricing_id'])?$post['pricing_id']:'';
        $mainImageUrl = '';
        $beneficiaryImageUrl = '';
        if(
            $beneficiary_account_name && $beneficiary_account_number && $beneficiary_bank && $beneficiary_ifsc && $fund_required && $title && $story
            && $no_of_days && $name && $email && $phone_number && $relation_master_id && $campaign_id
        ){
            $date = date('Y-m-d');
            $closing_date = date('Y-m-d', strtotime($date. ' + '.$no_of_days.' days'));
            $modelFundraiser = new FundraiserScheme;
            $modelFundraiser->name = $name;
            $modelFundraiser->email = $email;
            $modelFundraiser->phone_number = $phone_number;
            $modelFundraiser->country_code = $country_code;
            $modelFundraiser->relation_master_id = $relation_master_id;
            $modelFundraiser->patient_name = $patient_name;
            $modelFundraiser->health_issue = $health_issue;
            $modelFundraiser->hospital = $hospital;
            $modelFundraiser->city = $city;
            $modelFundraiser->beneficiary_account_name = $beneficiary_account_name;
            $modelFundraiser->beneficiary_account_number = $beneficiary_account_number;
            $modelFundraiser->beneficiary_bank = $beneficiary_bank;
            $modelFundraiser->beneficiary_ifsc = $beneficiary_ifsc;
            $modelFundraiser->campaign_id = $campaign_id;
            $modelFundraiser->title = $title;
            $modelFundraiser->fund_required = $fund_required;
            $modelFundraiser->story = $story;
            $modelFundraiser->closing_date = $closing_date;
            $modelFundraiser->created_by = $userId;
            $modelFundraiser->pricing_id = $pricing_id;
            $modelFundraiser->save(false);
            $main_image = UploadedFile::getInstanceByName('main_image');
            if($main_image && !empty($main_image)){
                $imageLocation = Yii::$app->params['upload_path_fundraiser_images'];
                $modelUser = new User;
                $saveImage = $modelUser->uploadAndSave($main_image,$imageLocation);
                if($saveImage){
                    $modelFundraiser->image_url = $saveImage;
                }
            }
            $beneficiary_image = UploadedFile::getInstanceByName('beneficiary_image');
            if($beneficiary_image && !empty($beneficiary_image)){
                $imageLocation = Yii::$app->params['upload_path_fundraiser_images'];
                $modelUser = new User;
                $saveImage1 = $modelUser->uploadAndSave($beneficiary_image,$imageLocation);
                if($saveImage1){
                    $modelFundraiser->beneficiary_image = $saveImage1;
                }
            }
            $modelFundraiser->save(false);
            $upload_documents = UploadedFile::getInstancesByName('upload_documents');
            if($upload_documents){
                foreach($upload_documents as $documents){
                    if($documents && !empty($documents)){
                        $imageLocation = Yii::$app->params['upload_path_fundraiser_documents'];
                        $modelUser = new User;
                        $saveImage2 = $modelUser->uploadAndSave($documents,$imageLocation);
                        if($saveImage2){
                            $modelFundraiserDocument = new FundraiserSchemeDocuments;
                            $modelFundraiserDocument->fundraiser_scheme_id = $modelFundraiser->id;
                            $modelFundraiserDocument->doc_url = $saveImage2;
                            $modelFundraiserDocument->file_type = ($documents->extension)?$documents->extension:'pdf';
                            $modelFundraiserDocument->save(false);
                        }
                    }
                }
            }
            $success = true;
            $modelFundraiser->save(false);
            
             $modelFundraiserdate = new FundraiserDates;
             $modelFundraiserdate->fundraiser_id=$modelFundraiser->id;
             $modelFundraiserdate->start_date=$modelFundraiser->created_at;
             $modelFundraiserdate->closing_date=$closing_date;
             $modelFundraiserdate->no_of_days=$no_of_days;
             $modelFundraiserdate->save(false);
             
             $modelDeducted = new DeductedAmount;
             $modelDeducted->fundraiser_id=$modelFundraiser->id;
             $modelDeducted->donor_name=$modelFundraiser->name;
             $modelDeducted->donor_email=$modelFundraiser->email;
             $modelDeducted->amount=$modelFundraiser->fund_required;
             $modelDeducted->save(false);
            

            // $modelPoint = Point::find()->where(['status'=>1,'title'=>'start-fundraise'])->one();
            // if($modelPoint && $modelPoint->point){
            //     $point = $modelPoint->point;
            //     $user_details->points = $user_details->points + $point;
            //     $user_details->save(false);
            // }
            
            //  if($customer_id){
            //     $api_key = KEY_ID;
            //      $api_secret = KEY_SECRET;
            //     $api = new Api($api_key, $api_secret);
            //     $virtualAccount = $api->virtualAccount->create(
            //         array(
            //             'receiver_types' => array(
            //                 'bank_account'
            //             ),
            //             'description' => 'Fundraiser title = '.$title,
            //             'customer_id' => $customer_id,
            //             'notes' => array(
            //                 'fundraiser_id' => $modelFundraiser->id
            //             )
            //         )
            //     );
            //     $modelFundraiser->virtual_account_id = $virtualAccount['id'];
            //     $modelFundraiser->save(false);
            //     $virtualAccount = $api->virtualAccount->fetch($virtualAccount['id']);
            //     if($virtualAccount){
            //         $modelFundraiser->virtual_account_name = $virtualAccount['name'];
            //         $modelFundraiser->virtual_account_number = $virtualAccount['receivers']['0']['account_number'];
            //         $modelFundraiser->virtual_account_ifsc = $virtualAccount['receivers']['0']['ifsc'];
            //         $modelFundraiser->virtual_account_type = 'Current';
            //         $modelFundraiser->save(false);
            //     }
            // }


            $msg = "FundRaiser Scheme Added successfully";

            Yii::$app->email->sendFundraiserUser($modelFundraiser);
            Yii::$app->email->sendFundraiserAdmin($modelFundraiser);
            $title = "Welcome to Crowd Works India Foundation Fundraising Platform,".$modelFundraiser->name." ! Start making a difference by setting up your fundraiser today. Visit crowdworksindia.org for more info.";
            $value = $userId;
            $type = 'fundraiser';
            $typeVal = $modelFundraiser->id;
            Yii::$app->notification->sendToOneUser($title,$value,$type,$typeVal);
            $sendSms = (new Account)->sendSMS(SMS_KEY, SMS_USERID, $country_code, $phone_number,$title);

        }else{
            $msg = 'campaign_id && beneficiary_account_name && beneficiary_account_number && beneficiary_bank && beneficiary_ifsc && fund_required && title && story && no_of_days && name && email && phone_number && relation_master_id cannot be blank';
        }
        $ret = [
            'statusCode' => $statusCode,
            'success' => $success,
            'message' => $msg,
            //'vanDetails' => $van_account_details
        ];
        return $ret;
    }
    public function actionFundraiserDetail(){
        header('Access-Control-Allow-Origin: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $get = Yii::$app->request->get();
        $fundraiser_id = isset($get['fundraiser_id'])?$get['fundraiser_id']:'';
        if(!$fundraiser_id){
            $msg = 'Fundraiser Id cannot be blank';
            $ret = [
                'statusCode' => $statusCode,
                'success' => $success,
                'message' => $msg
            ];
            return $ret;
        }
        $date = date('Y-m-d');
        $model = FundraiserScheme::find()
        ->where(['fundraiser_scheme.status'=>1,'fundraiser_scheme.id'=>$fundraiser_id,'is_approved'=>1])->andWhere(['>=','closing_date',$date])->one();
        if($model){
            if($fundraiser_id){
                $modelCampaigner = User::find()->where(['id'=>$model->created_by])->select('name,email,phone_number,country_code,image_url,date_of_birth')->one();
                
            }else{
                $modelCampaigner = null;
            }
            $modelFundraiserDocuments = FundraiserSchemeDocuments::find()->where(['fundraiser_scheme_id'=>$model->id,'status'=>1])->all();
            $fund_raised = Donation::find()->where(['status'=>1,'fundraiser_id'=>$fundraiser_id])->sum('amount');
            $baseUrl = Yii::$app->params['base_path_fundraiser_images'];
            $documentBaseUrl = Yii::$app->params['base_path_fundraiser_documents'];
            $campaignerBaseUrl = Yii::$app->params['base_path_profile_images'];

            $webBaseUrl = 'https://www.cocoalabs.in/ngo-web/ngo/fund-raiser-detail/';
            $ret = [
                'statusCode' => 200,
                'baseUrl' => $baseUrl,
                'webBaseUrl' => $webBaseUrl,
                'documentBaseUrl' => $documentBaseUrl,
                'campaignerBaseUrl' => $campaignerBaseUrl,
                'fund_raised' => (int) $fund_raised,
                'fundraiserDetails' => $model,
                'campaignerDetails' => $modelCampaigner,
                'fundraiserDocuments' => $modelFundraiserDocuments,
                'message' => 'Listed Successfully',
                'success' => true
            ];
            return $ret;
        }else{
            $msg = 'Invalid Fundraiser Id';
            $ret = [
                'statusCode' => $statusCode,
                'success' => $success,
                'message' => $msg
            ];
            return $ret;
        }
    }
    
    public function actionMyFundraiserDetail(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $get = Yii::$app->request->get();
        $fundraiser_id = isset($get['fundraiser_id'])?$get['fundraiser_id']:'';
        if(!$fundraiser_id){
            $msg = 'Fundraiser Id cannot be blank';
            $ret = [
                'statusCode' => $statusCode,
                'success' => $success,
                'message' => $msg
            ];
            return $ret;
        }
        $headers = getallheaders();
        $api_token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');
        if(!$api_token){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $token = (new Account)->getBearerToken($api_token);
        $validateToken = (new Account)->validateToken($token);
        if(!$validateToken){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $user_details = (new Account)->getCusomerDetailsByAPI($token);
        if(!$user_details){
            Yii::$app->response->statusCode = 401;
            $msg = "Somthing went wrong.";
            $ret = [
                'message' => $msg,
                'statusCode' => 401,
                'success' => $success
            ];
            return $ret;
        }
        $date = date('Y-m-d');
        $model = FundraiserScheme::find()->where(['fundraiser_scheme.status'=>1,'fundraiser_scheme.id'=>$fundraiser_id])->one();
        if($model->created_by != $user_details->id){
            $model = FundraiserScheme::find()->where(['fundraiser_scheme.status'=>1,'fundraiser_scheme.id'=>$fundraiser_id,'is_approved'=>1])->one();
        }
        if($model){
            if($fundraiser_id){
                $modelCampaigner = User::find()->where(['id'=>$model->created_by])->select('name,email,phone_number,country_code,image_url,date_of_birth')->one();
            }else{
                $modelCampaigner = null;
            }
            $modelFundraiserDocuments = FundraiserSchemeDocuments::find()->where(['fundraiser_scheme_id'=>$model->id,'status'=>1])->all();
            $fund_raised = Donation::find()->where(['status'=>1,'fundraiser_id'=>$fundraiser_id])->sum('amount');
            $baseUrl = Yii::$app->params['base_path_fundraiser_images'];
            $documentBaseUrl = Yii::$app->params['base_path_fundraiser_documents'];
            $campaignerBaseUrl = Yii::$app->params['base_path_profile_images'];

            $webBaseUrl = 'https://www.cocoalabs.in/ngo-web/ngo/fund-raiser-detail/';
            $ret = [
                'statusCode' => 200,
                'baseUrl' => $baseUrl,
                'webBaseUrl' => $webBaseUrl,
                'documentBaseUrl' => $documentBaseUrl,
                'campaignerBaseUrl' => $campaignerBaseUrl,
                'fund_raised' => (int) $fund_raised,
                'fundraiserDetails' => $model,
                'campaignerDetails' => $modelCampaigner,
                'fundraiserDocuments' => $modelFundraiserDocuments,
                'message' => 'Listed Successfully',
                'success' => true
            ];
            return $ret;
        }else{
            $msg = 'Invalid Fundraiser Id';
            $ret = [
                'statusCode' => $statusCode,
                'success' => $success,
                'message' => $msg
            ];
            return $ret;
        }
    }
    public function actionFundraiserDetailApi(){
        header('Access-Control-Allow-Origin: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $get = Yii::$app->request->get();
        $fundraiser_id = isset($get['fundraiser_id'])?$get['fundraiser_id']:'';
        if(!$fundraiser_id){
            $msg = 'Fundraiser Id cannot be blank';
            $ret = [
                'statusCode' => $statusCode,
                'success' => $success,
                'message' => $msg
            ];
            return $ret;
        }
        $model = FundraiserScheme::find()
        ->where(['fundraiser_scheme.status'=>1,'fundraiser_scheme.id'=>$fundraiser_id])->one();
        if($fundraiser_id){
            $modelCampaigner = User::find()->where(['id'=>$model->created_by])->select('name,email,phone_number,country_code,image_url,date_of_birth')->one();
        }else{
            $modelCampaigner = null;
        }
        $modelFundraiserDocuments = FundraiserSchemeDocuments::find()->where(['fundraiser_scheme_id'=>$model->id,'status'=>1])->all();
        $modelDonation = Donation::find()->where(['status'=>1,'fundraiser_id'=>$fundraiser_id]);
        $fund_raised = $modelDonation->sum('amount');
        $baseUrl = Yii::$app->params['base_path_fundraiser_images'];
        $documentBaseUrl = Yii::$app->params['base_path_fundraiser_documents'];
        $campaignerBaseUrl = Yii::$app->params['base_path_profile_images'];

        $supQuery= \Yii::$app->db->createCommand(" SELECT donation.name,donation.email,donation.amount,donation.show_donor_information,user.image_url as image_url  
        FROM donation LEFT JOIN user ON user.id = donation.user_id WHERE donation.status = 1 AND donation.fundraiser_id = '$fundraiser_id' LIMIT 5");
          
        $supportersArray = $supQuery->queryAll();
        $supporters = [];
        foreach($supportersArray as $supporter){
            $supporters[] = array(
                'name' => $supporter['name'],
                'email' => $supporter['email'],
                'amount' => (int) $supporter['amount'],
                'image_url' => $supporter['image_url'],
                'show_donor_information' => (int) $supporter['show_donor_information']
            );
        }
        $donorQuery= \Yii::$app->db->createCommand(" SELECT donation.name,donation.email,donation.amount,donation.show_donor_information,user.image_url as image_url 
        FROM donation LEFT JOIN user ON user.id = donation.user_id WHERE donation.status = 1 AND donation.fundraiser_id = '$fundraiser_id' ORDER BY donation.amount DESC LIMIT 5");
          
        $topDonorsArray = $donorQuery->queryAll();
        $topDonors = [];
        foreach($topDonorsArray as $topDonor){
            $topDonors[] = array(
                'name' => $topDonor['name'],
                'email' => $topDonor['email'],
                'amount' => (int) $topDonor['amount'],
                'image_url' => $topDonor['image_url'],
                'show_donor_information' => (int) $supporter['show_donor_information']
            );
        }
        $supportersCount = $modelDonation->count();

        $qry = " select fundraiser_scheme_comment.comment,fundraiser_scheme_comment.created_at,user.name,user.image_url from fundraiser_scheme_comment 
        left join fundraiser_scheme on fundraiser_scheme.id = fundraiser_scheme_comment.fundraiser_id left join user on user.id = fundraiser_scheme_comment.user_id 
        where fundraiser_scheme_comment.status = 1 and fundraiser_scheme_comment.fundraiser_id = '$fundraiser_id' limit 5 ";
        
        $comQuery = \Yii::$app->db->createCommand($qry);
        $comments = $comQuery->queryAll();

        $campaignDetail = Campaign::find()->where(['id'=>$model->campaign_id])->one();

        $webBaseUrl = 'https://crowdworksindia.org/#/fund-raiser-detail/'.$fundraiser_id;
        $supportersBaseUrl = Yii::$app->params['base_path_profile_images'];
        $topDonorsBaseUrl = Yii::$app->params['base_path_profile_images'];
        $commentsBaseUrl = Yii::$app->params['base_path_profile_images'];
        $ret = [
            'statusCode' => 200,
            'baseUrl' => $baseUrl,
            'webBaseUrl' => $webBaseUrl,
            'documentBaseUrl' => $documentBaseUrl,
            'campaignerBaseUrl' => $campaignerBaseUrl,
            'supportersBaseUrl' => $supportersBaseUrl,
            'topDonorsBaseUrl' => $topDonorsBaseUrl,
            'commentsBaseUrl' => $commentsBaseUrl,
            'supportersCount' => (int) $supportersCount,
            'fund_raised' => (int) $fund_raised,
            'fundraiserDetails' => $model,
            'campaignerDetails' => $modelCampaigner,
            'fundraiserDocuments' => $modelFundraiserDocuments,
            'supporters' => $supporters,
            'topDonors' => $topDonors,
            'comments' => $comments,
            'campaignDetail' => ($campaignDetail)?$campaignDetail:null,
            'message' => 'Listed Successfully',
            'success' => true
        ];
        return $ret;
    }
    public function actionMyFundraisers(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        $headers = getallheaders();
        $api_token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');
        if(!$api_token){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $token = (new Account)->getBearerToken($api_token);
        $validateToken = (new Account)->validateToken($token);
        if(!$validateToken){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $user_details = (new Account)->getCusomerDetailsByAPI($token);
        if(!$user_details){
            Yii::$app->response->statusCode = 401;
            $msg = "Somthing went wrong.";
            $ret = [
                'message' => $msg,
                'statusCode' => 401,
                'success' => $success
            ];
            return $ret;
        }
        $query = FundraiserScheme::find()->where(['status'=>1,'created_by'=>$user_details->id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [$page, $per_page]
            ]
        ]);
        $hasNextPage = false;
        if(($page*$per_page) < ($query->count())){
            $hasNextPage = true;
        }
        $baseUrl = Yii::$app->params['base_path_fundraiser_images'];
        $ret = [
            'statusCode' => 200,
            'baseUrl' => $baseUrl,
            'list' => $dataProvider,
            'page' => (int) $page,
            'perPage' => (int) $per_page,
            'hasNextPage' => $hasNextPage,
            'totalCount' => (int) $query->count(),
            'message' => 'Listed Successfully',
            'success' => true
        ];
        return $ret;
    }
    public function actionUpdateFundraiser(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $post = Yii::$app->request->post();
        $headers = getallheaders();
        $api_token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');
        if(!$api_token){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $token = (new Account)->getBearerToken($api_token);
        $validateToken = (new Account)->validateToken($token);
        if(!$validateToken){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $user_details = (new Account)->getCusomerDetailsByAPI($token);
        if(!$user_details){
            Yii::$app->response->statusCode = 401;
            $msg = "Somthing went wrong.";
            $ret = [
                'message' => $msg,
                'statusCode' => 401,
                'success' => $success
            ];
            return $ret;
        }
        $fundraiserId = isset($post['fundraiser_id'])?$post['fundraiser_id']:'';
        if(!$fundraiserId){
            $msg = "Fundraiser Id cannot be blank.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $userId = $user_details->id;
        $modelFundraiser = FundraiserScheme::find()->where(['status'=>1,'created_by'=>$userId,'id'=>$fundraiserId])->one();
        if(!$modelFundraiser){
            $msg = "Invalid Fundraiser Id.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $campaign_id = isset($post['campaign_id'])?$post['campaign_id']:$modelFundraiser->campaign_id;
        $name = isset($post['name'])?$post['name']:$modelFundraiser->name;
        $email = isset($post['email'])?$post['email']:$modelFundraiser->email;
        $phone_number = isset($post['phone_number'])?$post['phone_number']:$modelFundraiser->phone_number;
        $country_code = isset($post['country_code'])?$post['country_code']:$modelFundraiser->country_code;
        $relation_master_id = isset($post['relation_master_id'])?$post['relation_master_id']:$modelFundraiser->relation_master_id;
        $patient_name = isset($post['patient_name'])?$post['patient_name']:$modelFundraiser->patient_name;
        $health_issue = isset($post['health_issue'])?$post['health_issue']:$modelFundraiser->health_issue;
        $hospital = isset($post['hospital'])?$post['hospital']:$modelFundraiser->hospital;
        $city = isset($post['city'])?$post['city']:$modelFundraiser->city;
        $beneficiary_account_name = isset($post['beneficiary_account_name'])?$post['beneficiary_account_name']:$modelFundraiser->beneficiary_account_name;
        $beneficiary_account_number = isset($post['beneficiary_account_number'])?$post['beneficiary_account_number']:$modelFundraiser->beneficiary_account_number;
        $beneficiary_bank = isset($post['beneficiary_bank'])?$post['beneficiary_bank']:$modelFundraiser->beneficiary_bank;
        $beneficiary_ifsc = isset($post['beneficiary_ifsc'])?$post['beneficiary_ifsc']:$modelFundraiser->beneficiary_ifsc;
        $fund_required = isset($post['fund_required'])?$post['fund_required']:$modelFundraiser->fund_required;
        $title = isset($post['title'])?$post['title']:$modelFundraiser->title;
        $no_of_days = isset($post['no_of_days'])?$post['no_of_days']:'';
        $story = isset($post['story'])?$post['story']:$modelFundraiser->story;
        $pricing_id = isset($post['pricing_id'])?$post['pricing_id']:$modelFundraiser->pricing_id;
        $date = date('Y-m-d');
        if($no_of_days){
            $closing_date = date('Y-m-d', strtotime($date. ' + '.$no_of_days.' days'));
        }else{
            $closing_date = $modelFundraiser->closing_date;
        }

        $modelFundraiser->name = $name;
        $modelFundraiser->email = $email;
        $modelFundraiser->phone_number = $phone_number;
        $modelFundraiser->country_code = $country_code;
        $modelFundraiser->relation_master_id = $relation_master_id;
        $modelFundraiser->patient_name = $patient_name;
        $modelFundraiser->health_issue = $health_issue;
        $modelFundraiser->hospital = $hospital;
        $modelFundraiser->city = $city;
        $modelFundraiser->beneficiary_account_name = $beneficiary_account_name;
        $modelFundraiser->beneficiary_account_number = $beneficiary_account_number;
        $modelFundraiser->beneficiary_bank = $beneficiary_bank;
        $modelFundraiser->beneficiary_ifsc = $beneficiary_ifsc;
        $modelFundraiser->campaign_id = $campaign_id;
        $modelFundraiser->title = $title;
        $modelFundraiser->fund_required = $fund_required;
        $modelFundraiser->story = $story;
        $modelFundraiser->closing_date = $closing_date;
        $modelFundraiser->created_by = $userId;
        $modelFundraiser->pricing_id = $pricing_id;
        $modelFundraiser->save(false);

        $oldMainImage = $modelFundraiser->image_url;
        $oldBeneficiaryImage = $modelFundraiser->beneficiary_image;
        $main_image = UploadedFile::getInstanceByName('main_image');
        if($main_image && !empty($main_image)){
            $imageLocation = Yii::$app->params['upload_path_fundraiser_images'];
            $modelUser = new User;
            $saveImage = $modelUser->uploadAndSave($main_image,$imageLocation);
            if($saveImage){
                $modelFundraiser->image_url = $saveImage;
            }
        }else{
            $modelFundraiser->image_url = $oldMainImage;
        }
        $beneficiary_image = UploadedFile::getInstanceByName('beneficiary_image');
        if($beneficiary_image && !empty($beneficiary_image)){
            $imageLocation = Yii::$app->params['upload_path_fundraiser_images'];
            $modelUser = new User;
            $saveImage1 = $modelUser->uploadAndSave($beneficiary_image,$imageLocation);
            if($saveImage1){
                $modelFundraiser->beneficiary_image = $saveImage1;
            }
        }else{
            $modelFundraiser->beneficiary_image = $oldBeneficiaryImage;
        }
        $modelFundraiser->save(false);
        $success = true;
        $modelFundraiser->save(false);
        $msg = "FundRaiser Scheme Updated successfully";
        
        $ret = [
            'statusCode' => $statusCode,
            'success' => $success,
            'message' => $msg
        ];
        return $ret;
    }
    public function actionWithdraw(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $post = Yii::$app->request->post();
        $headers = getallheaders();
        $api_token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');
        if(!$api_token){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $token = (new Account)->getBearerToken($api_token);
        $validateToken = (new Account)->validateToken($token);
        if(!$validateToken){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $user_details = (new Account)->getCusomerDetailsByAPI($token);
        if(!$user_details){
            Yii::$app->response->statusCode = 401;
            $msg = "Somthing went wrong.";
            $ret = [
                'message' => $msg,
                'statusCode' => 401,
                'success' => $success
            ];
            return $ret;
        }
        $fundraiserId = isset($post['fundraiser_id'])?$post['fundraiser_id']:'';
        if(!$fundraiserId){
            $msg = "Fundraiser Id cannot be blank.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $userId = $user_details->id;
        $modelFundraiser = FundraiserScheme::find()->where(['status'=>1,'created_by'=>$userId,'id'=>$fundraiserId])->one();
        if(!$modelFundraiser){
            $msg = "Invalid Fundraiser Id.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $modelFundraiser->status = 0;
        $modelFundraiser->is_approved = 2;
        $modelFundraiser->save(false);
        $success = true;
        $msg = "Fundraiser Scheme Canceled Successfully";
        $ret = [
            'message' => $msg,
            'statusCode' => 200,
            'success' => $success
        ];
        return $ret;
    }
    public function actionUploadDocument(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $post = Yii::$app->request->post();
        $headers = getallheaders();
        $api_token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');
        if(!$api_token){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $token = (new Account)->getBearerToken($api_token);
        $validateToken = (new Account)->validateToken($token);
        if(!$validateToken){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $user_details = (new Account)->getCusomerDetailsByAPI($token);
        if(!$user_details){
            Yii::$app->response->statusCode = 401;
            $msg = "Somthing went wrong.";
            $ret = [
                'message' => $msg,
                'statusCode' => 401,
                'success' => $success
            ];
            return $ret;
        }
        $fundraiserId = isset($post['fundraiser_id'])?$post['fundraiser_id']:'';
        if(!$fundraiserId){
            $msg = "Fundraiser Id cannot be blank.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $userId = $user_details->id;
        $modelFundraiser = FundraiserScheme::find()->where(['status'=>1,'created_by'=>$userId,'id'=>$fundraiserId])->one();
        if(!$modelFundraiser){
            $msg = "Invalid Fundraiser Id.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $upload_documents = UploadedFile::getInstanceByName('upload_document');
        if(!$upload_documents){
            $msg = "Document cannot be blank.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $imageLocation = Yii::$app->params['upload_path_fundraiser_documents'];
        $modelFundraiserDocument = new FundraiserSchemeDocuments;
        $modelUser = new User;
        $saveImage = $modelUser->uploadAndSave($upload_documents,$imageLocation);
        if($saveImage){
            $modelFundraiserDocument->fundraiser_scheme_id = $modelFundraiser->id;
            $modelFundraiserDocument->doc_url = $saveImage;
            $modelFundraiserDocument->file_type = ($upload_documents->extension)?$upload_documents->extension:'pdf';
            $modelFundraiserDocument->save(false);
        }
        $success = true;
        $msg = "Document uploaded successfully.";
        $ret = [
            'message' => $msg,
            'statusCode' => 200,
            'success' => $success
        ];
        return $ret;
    }
    public function actionRemoveDocument(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $post = Yii::$app->request->post();
        $headers = getallheaders();
        $api_token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');
        if(!$api_token){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $token = (new Account)->getBearerToken($api_token);
        $validateToken = (new Account)->validateToken($token);
        if(!$validateToken){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $user_details = (new Account)->getCusomerDetailsByAPI($token);
        if(!$user_details){
            Yii::$app->response->statusCode = 401;
            $msg = "Somthing went wrong.";
            $ret = [
                'message' => $msg,
                'statusCode' => 401,
                'success' => $success
            ];
            return $ret;
        }
        $fundraiserId = isset($post['fundraiser_id'])?$post['fundraiser_id']:'';
        $id = isset($post['id'])?$post['id']:'';
        if(!$fundraiserId && !$id){
            $msg = "Fundraiser Id and Id cannot be blank.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $userId = $user_details->id;
        $modelFundraiser = FundraiserScheme::find()->where(['status'=>1,'created_by'=>$userId,'id'=>$fundraiserId])->one();
        if(!$modelFundraiser){
            $msg = "Invalid Fundraiser Id.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $modelFundraiserDocument = FundraiserSchemeDocuments::find()->where(['id'=>$id,'fundraiser_scheme_id'=>$fundraiserId])->one();
        if($modelFundraiserDocument){
            $modelFundraiserDocument->status = 0;
            $modelFundraiserDocument->save(false);

            $success = true;
            $msg = "Document removed successfully.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }else{
            $msg = "Invalid document Id.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
    }
    
    public function actionUpdateFundraiserApi(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $post = Yii::$app->request->post();
        $headers = getallheaders();
        $api_token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');
        if(!$api_token){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $token = (new Account)->getBearerToken($api_token);
        $validateToken = (new Account)->validateToken($token);
        if(!$validateToken){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $user_details = (new Account)->getCusomerDetailsByAPI($token);
        if(!$user_details){
            Yii::$app->response->statusCode = 401;
            $msg = "Somthing went wrong.";
            $ret = [
                'message' => $msg,
                'statusCode' => 401,
                'success' => $success
            ];
            return $ret;
        }
        $fundraiserId = isset($post['fundraiser_id'])?$post['fundraiser_id']:'';
        if(!$fundraiserId){
            $msg = "Fundraiser Id cannot be blank.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $userId = $user_details->id;
        $modelFundraiser = FundraiserScheme::find()->where(['status'=>1,'created_by'=>$userId,'id'=>$fundraiserId])->one();
        if(!$modelFundraiser){
            $msg = "Invalid Fundraiser Id.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $modelFundraiserUpdateRequest = FundraiserSchemeUpdateRequest::find()->where(['status'=>1,'fundraiser_id'=>$modelFundraiser->id])->one();
        if($modelFundraiserUpdateRequest){
            $modelFundraiserUpdateRequest->status = 0;
            $modelFundraiserUpdateRequest->save(false);
        }
        $modelFundraiserUpdateRequest = new FundraiserSchemeUpdateRequest;

        $campaign_id = isset($post['campaign_id'])?$post['campaign_id']:$modelFundraiser->campaign_id;
        $name = isset($post['name'])?$post['name']:$modelFundraiser->name;
        $email = isset($post['email'])?$post['email']:$modelFundraiser->email;
        $phone_number = isset($post['phone_number'])?$post['phone_number']:$modelFundraiser->phone_number;
        $country_code = isset($post['country_code'])?$post['country_code']:$modelFundraiser->country_code;
        $relation_master_id = isset($post['relation_master_id'])?$post['relation_master_id']:$modelFundraiser->relation_master_id;
        $patient_name = isset($post['patient_name'])?$post['patient_name']:$modelFundraiser->patient_name;
        $health_issue = isset($post['health_issue'])?$post['health_issue']:$modelFundraiser->health_issue;
        $hospital = isset($post['hospital'])?$post['hospital']:$modelFundraiser->hospital;
        $city = isset($post['city'])?$post['city']:$modelFundraiser->city;
        $beneficiary_account_name = isset($post['beneficiary_account_name'])?$post['beneficiary_account_name']:$modelFundraiser->beneficiary_account_name;
        $beneficiary_account_number = isset($post['beneficiary_account_number'])?$post['beneficiary_account_number']:$modelFundraiser->beneficiary_account_number;
        $beneficiary_bank = isset($post['beneficiary_bank'])?$post['beneficiary_bank']:$modelFundraiser->beneficiary_bank;
        $beneficiary_ifsc = isset($post['beneficiary_ifsc'])?$post['beneficiary_ifsc']:$modelFundraiser->beneficiary_ifsc;
        $fund_required = isset($post['fund_required'])?$post['fund_required']:$modelFundraiser->fund_required;
        $title = isset($post['title'])?$post['title']:$modelFundraiser->title;
        $no_of_days = isset($post['no_of_days'])?$post['no_of_days']:'';
        $story = isset($post['story'])?$post['story']:$modelFundraiser->story;
        $pricing_id = isset($post['pricing_id'])?$post['pricing_id']:$modelFundraiser->pricing_id;
        $date = date('Y-m-d');
        if($no_of_days){
            $closing_date = date('Y-m-d', strtotime($date. ' + '.$no_of_days.' days'));
        }else{
            $closing_date = $modelFundraiser->closing_date;
        }

        $modelFundraiserUpdateRequest->name = $name;
        $modelFundraiserUpdateRequest->email = $email;
        $modelFundraiserUpdateRequest->phone_number = $phone_number;
        $modelFundraiserUpdateRequest->country_code = $country_code;
        $modelFundraiserUpdateRequest->relation_master_id = $relation_master_id;
        $modelFundraiserUpdateRequest->patient_name = $patient_name;
        $modelFundraiserUpdateRequest->health_issue = $health_issue;
        $modelFundraiserUpdateRequest->hospital = $hospital;
        $modelFundraiserUpdateRequest->city = $city;
        $modelFundraiserUpdateRequest->beneficiary_account_name = $beneficiary_account_name;
        $modelFundraiserUpdateRequest->beneficiary_account_number = $beneficiary_account_number;
        $modelFundraiserUpdateRequest->beneficiary_bank = $beneficiary_bank;
        $modelFundraiserUpdateRequest->beneficiary_ifsc = $beneficiary_ifsc;
        $modelFundraiserUpdateRequest->campaign_id = $campaign_id;
        $modelFundraiserUpdateRequest->title = $title;
        $modelFundraiserUpdateRequest->fund_required = $fund_required;
        $modelFundraiserUpdateRequest->story = $story;
        $modelFundraiserUpdateRequest->closing_date = $closing_date;
        $modelFundraiserUpdateRequest->created_by = $userId;
        $modelFundraiserUpdateRequest->pricing_id = $pricing_id;
        $modelFundraiserUpdateRequest->fundraiser_id = $modelFundraiser->id;
        $modelFundraiserUpdateRequest->save(false);

        $oldMainImage = $modelFundraiser->image_url;
        $oldBeneficiaryImage = $modelFundraiser->beneficiary_image;
        $main_image = UploadedFile::getInstanceByName('main_image');
        if($main_image && !empty($main_image)){
            $imageLocation = Yii::$app->params['upload_path_fundraiser_images'];
            $modelUser = new User;
            $saveImage = $modelUser->uploadAndSave($main_image,$imageLocation);
            if($saveImage){
                $modelFundraiserUpdateRequest->image_url = $saveImage;
            }
        }else{
            $modelFundraiserUpdateRequest->image_url = $oldMainImage;
        }
        $beneficiary_image = UploadedFile::getInstanceByName('beneficiary_image');
        if($beneficiary_image && !empty($beneficiary_image)){
            $imageLocation = Yii::$app->params['upload_path_fundraiser_images'];
            $modelUser = new User;
            $saveImage1 = $modelUser->uploadAndSave($beneficiary_image,$imageLocation);
            if($saveImage1){
                $modelFundraiserUpdateRequest->beneficiary_image = $saveImage1;
            }
        }else{
            $modelFundraiserUpdateRequest->beneficiary_image = $oldBeneficiaryImage;
        }
        $modelFundraiserUpdateRequest->save(false);
        $success = true;
        $modelFundraiserUpdateRequest->save(false);
        $msg = "FundRaiser Scheme Updated successfully";
        
        $ret = [
            'statusCode' => $statusCode,
            'success' => $success,
            'message' => $msg
        ];
        return $ret;
    }
    
    public function actionDonateNgo()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $post = Yii::$app->request->post();
        $donor_type = isset($post['donor_type'])?$post['donor_type']:'';
        $fundraiser_id = isset($post['fundraiser_id'])?$post['fundraiser_id']:'';
        $user_id = isset($post['user_id'])?$post['user_id']:'';
        $amount = isset($post['amount'])?$post['amount']:'';
        $name = isset($post['name'])?$post['name']:'';
        $email = isset($post['email'])?$post['email']:'';
        $show_donor_information = isset($post['show_donor_information'])?$post['show_donor_information']:0;
        $transaction_id = isset($post['transaction_id'])?$post['transaction_id']:'';
    
            $certificate_name = isset($post['certificate_name'])?$post['certificate_name']:'';
            $certificate_address = isset($post['certificate_address'])?$post['certificate_address']:'';
            $certificate_phone = isset($post['certificate_phone'])?$post['certificate_phone']:'';
            $certificate_pan = isset($post['certificate_pan'])?$post['certificate_pan']:'';
           
               if($donor_type == 'Guest')
            {
                
                if($fundraiser_id == 'null')
                {
                   
                    if($amount && $name && $email)
                    {
                        $amount = isset($post['amount'])?$post['amount']:'';
                        $name = isset($post['name'])?$post['name']:'';
                        $email = isset($post['email'])?$post['email']:'';
                        $show_donor_information = isset($post['show_donor_information'])?$post['show_donor_information']:0;
                        $transaction_id = isset($post['transaction_id'])?$post['transaction_id']:'';
                        
                        $modelDonation = new Donation;
                        $modelDonation->name = $name;
                        $modelDonation->email = $email;
                        $modelDonation->amount = $amount;
                        $modelDonation->show_donor_information = $show_donor_information;
                        $modelDonation->transaction_id = $transaction_id;
                        $modelDonation->save(false);
                        
                         $donation_id =  $modelDonation->id;
            
                        if($modelDonation->save())
                        {
                            $modelTransaction = new Transaction;
                            $modelTransaction->donor_name = $name;
                            $modelTransaction->donor_email = $email;
                            $modelTransaction->payment_id = $transaction_id;
                            $modelTransaction->amount = $amount;
                            $modelTransaction->save(false);
                            
                        } 
                        
                        if($certificate_name && $certificate_address && $certificate_phone && $certificate_pan)
                        {
                              $modelCertificate = new Certificate;
                              $modelCertificate->name = $certificate_name;
                              $modelCertificate->address = $certificate_address;
                              $modelCertificate->phone_number = $certificate_phone;
                              $modelCertificate->pan_number = $certificate_pan;
                              $modelCertificate->amount = $amount;
                              $modelCertificate->fundraiser_id = $fundraiser_id;
                              $modelCertificate->donation_id = $donation_id;
                              $modelCertificate->save(false);
                        }

                        
                    $ret = [
                  'statusCode' => $statusCode,
                  'success' => true,
                  'message' =>"Donation received successfully"
                 ];
                 
                return $ret;
                     
                }
                    
                }
                else
                {
                
                $modelFundraiser = FundraiserScheme::find()->where(['id'=>$fundraiser_id])->one();
                if(!$modelFundraiser)
                {
                     $msg = "Invalid fundraiser id.";
                     $ret = [
                         'message' => $msg,
                         'statusCode' => 200,
                         'success' => $success
                        ];
                        return $ret;
                }
         
                $query = FundraiserScheme::find()->where(['id'=>$fundraiser_id])->one();
                
                if($fundraiser_id && $amount && $name && $email)
                {
                    
                        $modelDonation = new Donation;
                        $modelDonation->fundraiser_id = $fundraiser_id;
                        $modelDonation->name = $name;
                        $modelDonation->email = $email;
                        $modelDonation->amount = $amount;
                        $modelDonation->transaction_id = $transaction_id;
                        $modelDonation->show_donor_information = $show_donor_information;
                        $modelDonation->save(false);
                        
                         $donation_id =  $modelDonation->id;
                                
                        if($modelDonation->save(false))
                        {
                            
                            $modelTransaction = new Transaction;
                            $modelTransaction->donor_name = $name;
                            $modelTransaction->donor_email = $email;
                            $modelTransaction->fundraiser_id = $fundraiser_id;
                            $modelTransaction->payment_id = $transaction_id;
                            $modelTransaction->amount = $amount;
                            $modelTransaction->save(false);
                        }
                        if($modeldeduction->save())
                        {
                   
                          $query->fund_raised +=$amount;
                          $query->save(false);
                          $goal_amt=$query->fund_raised;
                          $fund_raised= $query->fund_raised;
                          $reduced_amt= $goal_amt - $fund_raised;
                        }
                    if($certificate_name && $certificate_address && $certificate_phone && $certificate_pan)
                    {
                            
                              $modelCertificate = new Certificate;
                              $modelCertificate->name = $certificate_name;
                              $modelCertificate->address = $certificate_address;
                              $modelCertificate->phone_number = $certificate_phone;
                              $modelCertificate->pan_number = $certificate_pan;
                              $modelCertificate->amount = $amount;
                              $modelCertificate->donation_id = $donation_id;
                              $modelCertificate->save(false);
                    }
                }    
                    else
                    {
                        $msg = 'Fundraiser, Amount, Name, Email cannot be blank';
                    }
             
                  $ret = [
                  'statusCode' => $statusCode,
                  'success' => true,
                  'message' =>"Donation received successfully"
                 ];
                return $ret;
            }
        }
        
        if($user_id && $amount && $name && $email)
        {
               
                $amount = isset($post['amount'])?$post['amount']:'';
                $name = isset($post['name'])?$post['name']:'';
                $email = isset($post['email'])?$post['email']:'';
                $show_donor_information = isset($post['show_donor_information'])?$post['show_donor_information']:0;
                $transaction_id = isset($post['transaction_id'])?$post['transaction_id']:'';
                
                    $modelDonation = new Donation;
                    $modelDonation->user_id = $user_id;
                    $modelDonation->name = $name;
                    $modelDonation->email = $email;
                    $modelDonation->amount = $amount;
                    $modelDonation->show_donor_information = $show_donor_information;
                    $modelDonation->transaction_id = $transaction_id;
                    $modelDonation->save(false);
                    
                     $donation_id =  $modelDonation->id;
            
                    if($modelDonation->save())
                    {  
                         $modelTransaction = new Transaction;
                         
                         $modelTransaction->donor_name = $name;
                         $modelTransaction->donor_email = $email;
                         $modelTransaction->payment_id = $transaction_id;
                         $modelTransaction->amount = $amount;
                         $modelTransaction->save(false);
                    }
                    
                    if($certificate_name && $certificate_address && $certificate_phone && $certificate_pan)
                        {
                           
                              $modelCertificate = new Certificate;
                              $modelCertificate->name = $certificate_name;
                              $modelCertificate->address = $certificate_address;
                              $modelCertificate->phone_number = $certificate_phone;
                              $modelCertificate->pan_number = $certificate_pan;
                              $modelCertificate->user_id = $user_id;
                              $modelCertificate->amount = $amount;
                              $modelCertificate->donation_id = $donation_id;
                              $modelCertificate->save(false);
                        }
                $ret = [
                   'statusCode' => $statusCode,
                   'success' => true,
                   'message' =>"Donation received successfully"
                ];
                return $ret;
        }
    } 
    
        
}


