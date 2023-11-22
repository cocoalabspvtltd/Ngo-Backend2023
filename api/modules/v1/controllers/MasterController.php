<?php

namespace api\modules\v1\controllers;
use yii;
use yii\rest\ActiveController;
use backend\models\RelationMaster;
use backend\models\FundraiserScheme;
use backend\models\User;
use api\modules\v1\models\Account;
use backend\models\Donation;
use backend\models\LoanDonation;
use backend\models\Log;
use backend\models\OurTeam;
use backend\models\PricingMaster;
use backend\models\PaymentOrder;
use backend\models\PaymentOrderList;
use backend\models\Campaign;
use backend\models\AgencyLandingPage;
use backend\models\FundraiserSchemeDocuments;
use yii\data\ActiveDataProvider;
use Razorpay\Api\Api;
use backend\models\Certificate;
use backend\models\Visitor;
use backend\models\Subscription;
use backend\models\Point;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class MasterController extends ActiveController
{
    public $modelClass = 'backend\models\RelationMaster';      
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }  
    public function  actionRelationMaster(){
        header('Access-Control-Allow-Origin: *');
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:25;
        $query = RelationMaster::find()->where(['status'=>1,'relation_status'=>1])->all();
         
        // $dataProvider = new ActiveDataProvider([
        //     'query' => $query,
        //     'pagination' => [
        //         'pageSizeLimit' => [$page, $per_page]
        //     ]
        // ]);
       
        // $hasNextPage = false;
        // if(($page*$per_page) < ($query->count())){
        //     $hasNextPage = true;
        // }
        $ret = [
            'statusCode' => 200,
            'list' => $query,
            'page' => (int) $page,
            'perPage' => (int) $per_page,
           // 'hasNextPage' => $hasNextPage,
            //'totalCount' => (int) $query->count(),
            'message' => 'Listed Successfully',
            'success' => true
        ];
        return $ret;
    }
    public function actionAndroidSearch(){
        header('Access-Control-Allow-Origin: *');
        $succcess = false;
        $statusCode = 200;
        $date = date('Y-m-d');
        $get = Yii::$app->request->get();
        $keyword = isset($get['keyword'])?$get['keyword']:'';
        $list = [];
        if($keyword){
            $query = FundraiserScheme::find()->where(['status'=>1,'is_approved'=>1]);
            $query->andWhere(['LIKE','title',$keyword])->andWhere(['>=','closing_date',$date]);
            $query->select('id,title');
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false
            ]);
            foreach($dataProvider->getModels() as $fundraiser){
                $list[] = [
                    'id' => $fundraiser->id,
                    'title' => $fundraiser->title,
                    'type' => 'fundraiser'
                ];
            }
        }
        $modelCampaign = Campaign::find()
        ->where(['LIKE','title',$keyword])->andWhere(['status'=>1])
        ->select('id,title')->all();
        foreach($modelCampaign as $campaign){
            $list[] = [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'type' => 'campaign'
            ];
        }        
        
        $ret = [
            'success' => true,
            'statusCode' => 200,
            'list' => $list,
            'message' => 'Listed Successfully'
        ];
        return $ret;
    }
    public function actionSearch(){
        header('Access-Control-Allow-Origin: *');
        $succcess = false;
        $statusCode = 200;
        $date = date('Y-m-d');
        $get = Yii::$app->request->get();
        $keyword = isset($get['keyword'])?$get['keyword']:'';
        if($keyword){
            $modelFundraiser = FundraiserScheme::find()
            ->where(['LIKE','title',$keyword])->andWhere(['status'=>1,'is_approved'=>1])->andWhere(['>=','closing_date',$date])
            ->select('id,title')->all();
            $list = [];
            foreach($modelFundraiser as $fundraiser){
                $list[] = [
                    'id' => $fundraiser->id,
                    'title' => $fundraiser->title,
                    'type' => 'fundraiser'
                ];
            }
            $modelCampaign = Campaign::find()
            ->where(['LIKE','title',$keyword])->andWhere(['status'=>1])
            ->select('id,title')->all();
            foreach($modelCampaign as $campaign){
                $list[] = [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'type' => 'campaign'
                ];
            }
            $ret = [
                'success' => true,
                'statusCode' => 200,
                'list' => $list,
                'message' => 'Listed Successfully'
            ];
            return $ret;
        }else{
            $msg = "Keyword cannot be blank";
            $ret = [
                'message' => $msg,
                'success' => $succcess,
                'statusCode' => $statusCode
            ];
            return $ret;
        }
    }
    public function actionStatitics(){
        $totalUsers = 0;
        $totalVisitors = 0;
        $totalFundraisers = 0;
        $totalCampaigns = 0;
        $totalSupporters = 0;
        $totalFundRaised = 0;
        $totalFundRequired = 0;
        $totalUsers = User::find()->where(['status'=>1,'role'=>'campaigner'])->count();
        $totalFundraisers = FundraiserScheme::find()
        ->leftJoin('user','user.id=fundraiser_scheme.created_by')
        ->where(['fundraiser_scheme.status'=>1])
        ->andWhere(['user.role'=>'campaigner'])->count();
        $totalCampaigns = FundraiserScheme::find()
        ->leftJoin('user','user.id=fundraiser_scheme.created_by')
        ->where(['fundraiser_scheme.status'=>1])
        ->andWhere(['user.role'=>'super-admin'])->count();
        $totalFundRequired = FundraiserScheme::find()
        ->leftJoin('user','user.id=fundraiser_scheme.created_by')
        ->where(['fundraiser_scheme.status'=>1])->sum('fund_required');
        $totalSupporters = Donation::find()->where(['status'=>1])->count();
        $totalFundRaised = Donation::find()->where(['status'=>1])->sum('amount');

        $all = [
            'totalUsers' => (int) $totalUsers,
            'totalVisitors' => $totalVisitors
        ];
        $campaigns = [
            'totalFundraisers' => (int) $totalFundraisers,
            'totalCampaigns' => (int) $totalCampaigns,
            'totalSupporters' => (int) $totalSupporters
        ];
        $funds = [
            'totalFundRaised' => (int) $totalFundRaised,
            'totalFundRequired' => (int) $totalFundRequired
        ];
        $ret = [
            'all' => $all,
            'campaigns' => $campaigns,
            'funds' => $funds,
            'success' => true
        ];
        return $ret;
    }
    public function  actionReport(){
        header('Access-Control-Allow-Origin: *');
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        
        $totalVisitors = Visitor::find()->where(['status'=>1])->count();
        $totalUsers = User::find()->where(['status'=>1,'role'=>'campaigner'])->count();
        $totalFundraisers = FundraiserScheme::find()
        ->leftJoin('user','user.id=fundraiser_scheme.created_by')
        ->where(['fundraiser_scheme.status'=>1])
        ->andWhere(['user.role'=>'campaigner'])->count();
        $totalCampaigns = FundraiserScheme::find()
        ->leftJoin('user','user.id=fundraiser_scheme.created_by')
        ->where(['fundraiser_scheme.status'=>1])
        ->andWhere(['user.role'=>'super-admin'])->count();
        $totalFundRequired = FundraiserScheme::find()
        ->leftJoin('user','user.id=fundraiser_scheme.created_by')
        ->where(['fundraiser_scheme.status'=>1])->sum('fund_required');
        $totalSupporters = Donation::find()->where(['status'=>1])->count();
        $totalFundRaised = Donation::find()->where(['status'=>1])->sum('amount');

        $all = [
            'totalUsers' => (int) $totalUsers,
            'totalVisitors' => (int) $totalVisitors
        ];
        $campaigns = [
            'totalFundraisers' => (int) $totalFundraisers,
            'totalCampaigns' => (int) $totalCampaigns,
            'totalSupporters' => (int) $totalSupporters
        ];
        $funds = [
            'totalFundRaised' => (int) $totalFundRaised,
            'totalFundRequired' => (int) $totalFundRequired
        ];
        $ret = [
            'items' => array(
                'all' => $all,
                'campaigns' => $campaigns,
                'funds' => $funds,
            ),
            'success' => true,
            'message' => 'Listed Successfully'
        ];
        return $ret;
    }
    public function actionGetApiKey(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $get = Yii::$app->request->get();
        $fundraiser_id = isset($get['fundraiser_id'])?$get['fundraiser_id']:'';
        $loan_id = isset($get['loan_id'])?$get['loan_id']:'';
        $headers = getallheaders();
        $api_token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');
        if(!$api_token){
            $msg = "Authentication failed";
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 400,
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
        $apiKey = KEY_ID;
        $customer_id = '';
         if($fundraiser_id == 'null')
           {
               
            $ret = [
            'apiKey' => $apiKey,
            'user_details' => $user_details,
            'customerId' => $customer_id,
            'statusCode' => 200,
            'message' => 'Listed Successfully',
            'success' => true,
            ];
             return $ret;
        
           }
        if($fundraiser_id){
            
            $query = FundraiserScheme::find()->where(['id'=>$fundraiser_id])->one();
            
            if($query->fund_raised == $query->fund_required)
            {
                   
            $ret = [
                'statusCode' => $statusCode,
                'success' => true,
                'message' =>"Goal Amount is Achived Now you can't pay to this foundation"
            ];
            return $ret; 
                    
            }
          
            $modelUser = User::find()
            ->leftJoin('fundraiser_scheme','fundraiser_scheme.created_by=user.id')
            ->where(['fundraiser_scheme.id'=>$fundraiser_id])->one();
            $customer_id = $modelUser->customer_id;
        }
       
        if($loan_id){
            $modelUser = User::find()
            ->leftJoin('loan','loan.created_by=user.id')
            ->where(['loan.id'=>$loan_id])->one();
            $customer_id = $modelUser->customer_id;
        }
        $ret = [
            'apiKey' => $apiKey,
            'user_details' => $user_details,
            'customerId' => $customer_id,
            'statusCode' => 200,
            'message' => 'Listed Successfully',
            'success' => true,
        ];
        return $ret;
    }
    public function actionGetOrderId(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $get = Yii::$app->request->get();
        $amount = isset($get['amount'])?$get['amount']:'';
        $fundraiser_id = isset($get['fundraiser_id'])?$get['fundraiser_id']:'';
        $loan_id = isset($get['loan_id'])?$get['loan_id']:'';
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
        if(!$amount){
            $msg = "amount cannot be blank.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $recipt_id = '';
        if($fundraiser_id){
            $recipt_id = $fundraiser_id;
        }elseif($loan_id){
            $recipt_id = $loan_id;
        }
        $amt = $amount * 100;
        $api_key = KEY_ID;
        $api_secret = KEY_SECRET;
        $api = new Api($api_key, $api_secret);
        $order = $api->order->create(
            array(
                'receipt' => $recipt_id,
                'amount' => $amt,
                'currency' => 'INR'
            )
        );
        $modelPaymentOrder = new PaymentOrder;
        $modelPaymentOrder->fundraiser_id = $fundraiser_id;
        $modelPaymentOrder->loan_id = $loan_id;
        // $modelPaymentOrder->user_id = $user_details->id;
        $modelPaymentOrder->order_id = $order->id;
        $modelPaymentOrder->amount = $amount;
        $modelPaymentOrder->converted_amount = $amt;
        $modelPaymentOrder->currency = 'INR';
        $modelPaymentOrder->save(false);
        // print_r($order);exit;

        $ret = [
            'orderId' => $order->id,
            'convertedAmount' => $amt,
            'statusCode' => 200,
            'message' => 'Listed Successfully',
            'success' => true,
        ];
        return $ret;
    }
    
    public function actionCapture_payment()
    {
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
        
        $amount = isset($post['amount'])?$post['amount']:'';
        $payment_id = isset($post['payment_id'])?$post['payment_id']:'';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/payments/'.$payment_id.'/capture');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"amount\": $amount,\n  \"currency\": \"INR\"\n}");
        curl_setopt($ch, CURLOPT_USERPWD, KEY_ID.":". KEY_SECRET);
        
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        $decode = json_decode($result);
    }
    
    public function  actionOurTeam(){
        header('Access-Control-Allow-Origin: *');
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        $query = OurTeam::find()->where(['status'=>1]);
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
        $baseUrl = Yii::$app->params['base_path_profile_images'];
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
    public function  actionPricing(){
        header('Access-Control-Allow-Origin: *');
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        $query = PricingMaster::find()->where(['status'=>1]);
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
        $ret = [
            'statusCode' => 200,
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
    public function actionGetPayment(){
        $post = Yii::$app->request->post();
        $model = new Log;
        $model->meta = json_encode($post);
        $model->save(false);

        if($post){
            $paymentEvent = $post['event'];
            if($paymentEvent == 'payment.authorized'){
                $payload = $post['payload'];
                if($payload){
                    $payment = $post['payload']['payment'];
                    if($payment){
                        $entity = $post['payload']['payment']['entity'];
                        if($entity){
                            $paymentId = $post['payload']['payment']['entity']['id'];
                            $convertedAmount = (($post['payload']['payment']['entity']['amount'])/100);
                            $customerId = ($post['payload']['payment']['entity']['customer_id'])?$post['payload']['payment']['entity']['customer_id']:'';
                            $notes = $post['payload']['payment']['entity']['notes'];
                            if($notes){
                                $type = $post['payload']['payment']['entity']['notes']['type'];
                                if($type == 'donate'){
                                    $fundraiserId = ($post['payload']['payment']['entity']['notes']['fid'])?(int) $post['payload']['payment']['entity']['notes']['fid']:null;
                                    $name = $post['payload']['payment']['entity']['notes']['nam'];
                                    $email = $post['payload']['payment']['entity']['notes']['eml'];
                                    $amount = (int) $post['payload']['payment']['entity']['notes']['amt'];
                                    $showDonorInformation = (int) $post['payload']['payment']['entity']['notes']['sdi'];
                                    $transactionId = $paymentId;
                                    $certificateName = isset($post['payload']['payment']['entity']['notes']['cna'])?$post['payload']['payment']['entity']['notes']['cna']:'';
                                    $certificateAddress = isset($post['payload']['payment']['entity']['notes']['cad'])?$post['payload']['payment']['entity']['notes']['cad']:'';
                                    $certificatePhone = isset($post['payload']['payment']['entity']['notes']['cph'])?$post['payload']['payment']['entity']['notes']['cph']:'';
                                    $certificatePan = isset($post['payload']['payment']['entity']['notes']['cpc'])?$post['payload']['payment']['entity']['notes']['cpc']:'';
                                    $donatedBy = isset($post['payload']['payment']['entity']['notes']['d_by'])?$post['payload']['payment']['entity']['notes']['d_by']:'';
                                    $subscribe = isset($post['payload']['payment']['entity']['notes']['sbscrb'])?$post['payload']['payment']['entity']['notes']['sbscrb']:'';

                                    $modelUser = User::find()->where(['id'=>$donatedBy])->one();

                                    $modelDonation = new Donation;
                                    $modelDonation->fundraiser_id = $fundraiserId;
                                    $modelDonation->user_id = ($donatedBy)?$donatedBy:null;
                                    $modelDonation->name = $name;
                                    $modelDonation->email = $email;
                                    $modelDonation->amount = $amount;
                                    $modelDonation->show_donor_information = $showDonorInformation;
                                    $modelDonation->transaction_id = $transactionId;
                                    $modelDonation->donated_by = ($donatedBy)?$donatedBy:null;
                                    $modelDonation->save(false);

                                    Yii::$app->email->sendDonationUser($email,$name,$amount);
                                    Yii::$app->email->sendDonationAdmin($email,$name,$amount);
                                    if($donatedBy){
                                        $title = "Donated Successfully";
                                        $value = $donatedBy;
                                        Yii::$app->notification->sendToOneUser($title,$value);
                                        $country_code = $modelUser->country_code;
                                        $phone_number = $modelUser->phone_number;
                                        $sendSms = (new Account)->sendSMS(SMS_KEY, SMS_USERID, $country_code, $phone_number,$title);
                                    }

                                    if($subscribe && $donatedBy){
                                        $modelSubscription = Subscription::find()->where(['status'=>1,'user_id'=>$donatedBy,'fundraiser_id'=>$fundraiserId])->one();
                                        if($modelSubscription){
                                            $modelSubscription->amount = $amount;
                                            $modelSubscription->save(false);
                                        }else{
                                            $modelSubscription = new Subscription;
                                            $modelSubscription->user_id = $donatedBy;
                                            $modelSubscription->fundraiser_id = $fundraiserId;
                                            $modelSubscription->amount = $amount;
                                            $modelSubscription->donation_id = $modelDonation->id;
                                            $modelSubscription->save(false);
                                        }
                                    }


                                    $api_key = KEY_ID;
                                    $api_secret = KEY_SECRET;
                                    $api = new Api($api_key, $api_secret);

                                    $modelFundraiser = FundraiserScheme::find()->where(['id'=>$fundraiserId])->one();
                                    $virtualAccountId = $modelFundraiser->virtual_account_id;
                                    if($virtualAccountId){
                                        $transfer = $api->payment->fetch($transactionId)->transfer(array(
                                            'transfers' => [
                                                [
                                                    'account' => $virtualAccountId,
                                                    'amount' => $amount,
                                                    'currency' => 'INR'
                                                ]
                                            ]
                                        ));
                                    }
                                    Yii::$app->email->sendReceipt($fundraiserId,$email,$name,$amount);

                                    if($certificateName && $certificatePhone && $certificatePan){
                                        $modelCertificate = new Certificate;
                                        $modelCertificate->name = $certificateName;
                                        $modelCertificate->address = $certificateAddress;
                                        $modelCertificate->phone_number = $certificatePhone;
                                        $modelCertificate->pan_number = $certificatePan;
                                        $modelCertificate->user_id = ($donatedBy)?$donatedBy:null;
                                        $modelCertificate->fundraiser_id = $fundraiserId;
                                        $modelCertificate->save(false);
                                    }

                                    $modelPoint = Point::find()->where(['status'=>1,'title'=>'donate'])->one();
                                    if($modelUser && $modelPoint && $modelPoint->point){
                                        $point = $modelPoint->point;
                                        $modelUser->points = $modelUser->points + $point;
                                        $modelUser->save(false);
                                    }

                                    $modelFundraiser = FundraiserScheme::find()->where(['id'=>$fundraiserId])->one();
                                    if($modelFundraiser){
                                        $totalAmountCollected = Donation::find()->where(['status'=>1,'fundraiser_id'=>$fundraiserId])->sum('amount');
                                        $requiredAmount = $modelFundraiser->fund_required;
                                        $fundRaised = $modelFundraiser->fund_raised;
                                        $modelFundraiser->fund_raised = $fundRaised + $amount;
                                        if($totalAmountCollected >= $requiredAmount){
                                            $modelFundraiser->is_amount_collected = 1;
                                        }
                                        $modelFundraiser->save(false);
                                    }
                                }
                                if($type == 'loan'){
                                    $loanId = (int) $post['payload']['payment']['entity']['notes']['lid'];
                                    // $name = $post['payload']['payment']['entity']['notes']['nam'];
                                    // $email = $post['payload']['payment']['entity']['notes']['eml'];
                                    // $showDonorInformation = (int) $post['payload']['payment']['entity']['notes']['sdi'];
                                    $amount = (int) $post['payload']['payment']['entity']['notes']['amt'];
                                    $transactionId = $paymentId;
                                    $certificateName = $post['payload']['payment']['entity']['notes']['cna'];
                                    $certificateAddress = isset($post['payload']['payment']['entity']['notes']['cad'])?$post['payload']['payment']['entity']['notes']['cad']:'';
                                    $certificatePhone = $post['payload']['payment']['entity']['notes']['cph'];
                                    $certificatePan = $post['payload']['payment']['entity']['notes']['cpc'];
                                    $donatedBy = isset($post['payload']['payment']['entity']['notes']['d_by'])?$post['payload']['payment']['entity']['notes']['d_by']:'';

                                    $modelDonation = new LoanDonation;
                                    $modelDonation->loan_id = $loanId;
                                    $modelDonation->user_id = ($donatedBy)?$donatedBy:null;
                                    $modelDonation->amount = $amount;
                                    $modelDonation->transaction_id = $transactionId;
                                    $modelDonation->donated_by = ($donatedBy)?$donatedBy:null;
                                    $modelDonation->save(false);

                                    // $api_key = KEY_ID;
                                    // $api_secret = KEY_SECRET;
                                    // $api = new Api($api_key, $api_secret);

                                    // $modelLoan = Loan::find()->where(['id'=>$loanId])->one();
                                    // $virtualAccountId = $modelLoan->virtual_account_id;
                                    // if($virtualAccountId){
                                    //     $transfer = $api->payment->fetch($transactionId)->transfer(
                                    //         array(
                                    //             'transfers' => [
                                    //                 [
                                    //                     'account' => $virtualAccountId,
                                    //                     'amount' => $amount,
                                    //                     'currency' => 'INR'
                                    //                 ]
                                    //             ]
                                    //         )
                                    //     );
                                    // }

                                    if($certificateName && $certificatePhone && $certificatePan){
                                        $modelCertificate = new Certificate;
                                        $modelCertificate->name = $certificateName;
                                        $modelCertificate->address = ($certificateAddress)?$certificateAddress:null;
                                        $modelCertificate->phone_number = $certificatePhone;
                                        $modelCertificate->pan_number = $certificatePan;
                                        $modelCertificate->user_id = ($donatedBy)?$donatedBy:null;
                                        $modelCertificate->fundraiser_id = $loanId;
                                        $modelCertificate->save(false);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    public function  actionPoint(){
        header('Access-Control-Allow-Origin: *');
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        $query = Point::find()->where(['status'=>1]);
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
        $ret = [
            'statusCode' => 200,
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
    public function actionLandingPage(){
        header('Access-Control-Allow-Origin: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $get = Yii::$app->request->get();
        $id = isset($get['id'])?$get['id']:'';
        if(!$id){
            $msg = 'ID cannot be blank';
            $ret = [
                'statusCode' => $statusCode,
                'success' => $success,
                'message' => $msg
            ];
            return $ret;
        }
        $modelAgencyLandingPage = AgencyLandingPage::find()->where(['id'=>$id])->one();
        if(!$modelAgencyLandingPage){
            $msg = 'Invalid ID.';
            $ret = [
                'statusCode' => $statusCode,
                'success' => $success,
                'message' => $msg
            ];
            return $ret;
        }
        $fundraiser_id = $modelAgencyLandingPage->fundraiser_scheme_id;
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

        $webBaseUrl = 'https://www.cocoalabs.in/ngo-landing-page/ngo/campaignDetail/';
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
            'agencyDetails' => $modelAgencyLandingPage,
            'success' => true
        ];
        return $ret;
    }
    
    public function actionPaymentOrderId()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $get = Yii::$app->request->get();
        $amount = isset($get['amount'])?$get['amount']:'';
        $name = isset($get['name'])?$get['name']:'';
        $email = isset($get['email'])?$get['email']:'';
        if(!$amount){
            $msg = "amount cannot be blank.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $recipt_id =$name;
        $amt = $amount * 100;
        $api_key = KEY_ID;
        $api_secret = KEY_SECRET;
        $api = new Api($api_key, $api_secret);
        $order = $api->order->create(
            array(
                'receipt' => $recipt_id,
                'amount' => $amt,
                'currency' => 'INR'
            )
        );
        $modelPaymentOrder = new PaymentOrderList;
        $modelPaymentOrder->name = $name;
        $modelPaymentOrder->email = $email;
        $modelPaymentOrder->order_id = $order->id;
        $modelPaymentOrder->amount = $amount;
        $modelPaymentOrder->converted_amount = $amt;
        $modelPaymentOrder->currency = 'INR';
        $modelPaymentOrder->save(false);
        // print_r($order);exit;

        $ret = [
            'apiKey' =>  $api_key,
            'orderId' => $order->id,
            'convertedAmount' => $amt,
            'statusCode' => 200,
            'message' => 'Listed Successfully',
            'success' => true,
        ];
        return $ret;
    }
    
    public function actionDonateNgo()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        
        $userId = isset($post['user_id'])?$post['user_id']:'';;
        $amount = isset($post['amount'])?$post['amount']:'';
        $name = isset($post['name'])?$post['name']:'';
        $email = isset($post['email'])?$post['email']:'';
        $show_donor_information = isset($post['show_donor_information'])?$post['show_donor_information']:0;
        $transaction_id = isset($post['transaction_id'])?$post['transaction_id']:'';
        
           if($user_id == null){
        
            $modelDonation = new Donation;
            $modelDonation->name = $name;
            $modelDonation->email = $email;
            $modelDonation->amount = $amount;
            $modelDonation->show_donor_information = $show_donor_information;
            $modelDonation->transaction_id = $transaction_id;
            $modelDonation->save(false);
            
            if($modelDonation->save())
            {
            $modelTransaction = new Transaction;
            $modelTransaction->donor_name = $name;
            $modelTransaction->donor_email = $email;
            $modelTransaction->payment_id = $transaction_id;
            $modelTransaction->amount = $amount;
            $modelTransaction->save(false);
              if($modelTransaction->save()){

              $query = FundraiserScheme::find()
              ->where(['id'=>$fundraiser_id])->one();

              $query->fund_raised +=$amount;
              $query->save(false);

              $goal_amt=$query->fund_raised;
              $fund_raised= $query->fund_raised;

             $reduced_amt= $goal_amt - $fund_raised;

             }
          }
        
        }
        
            $modelDonation = new Donation;
            $modelDonation->user_id = $userId;
            $modelDonation->name = $name;
            $modelDonation->email = $email;
            $modelDonation->amount = $amount;
            $modelDonation->show_donor_information = $show_donor_information;
            $modelDonation->transaction_id = $transaction_id;
            $modelDonation->save(false);
            
            if($modelDonation->save())
            {
            $modelTransaction = new Transaction;
            $modelTransaction->donor_name = $name;
            $modelTransaction->donor_email = $email;
            $modelTransaction->payment_id = $transaction_id;
            $modelTransaction->amount = $amount;
            $modelTransaction->save(false);
              if($modelTransaction->save()){

              $query = FundraiserScheme::find()
              ->where(['id'=>$fundraiser_id])->one();

              $query->fund_raised +=$amount;
              $query->save(false);

              $goal_amt=$query->fund_raised;
              $fund_raised= $query->fund_raised;

             $reduced_amt= $goal_amt - $fund_raised;

             }
          }
          $ret = [
            'statusCode' => $statusCode,
            'success' => $success,
            'message' =>"Donation received successfully"
          ];
          return $ret;
        
    }
    
}


