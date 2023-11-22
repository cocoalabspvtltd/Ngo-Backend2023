<?php

namespace api\modules\v1\controllers;

use yii;
use mpdf;
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
use backend\models\Campaign;
use backend\models\AgencyLandingPage;
use backend\models\TransferRequest;
use backend\models\FundraiserSchemeDocuments;
use yii\data\ActiveDataProvider;
use Razorpay\Api\Api;
use backend\models\Certificate;
use backend\models\Point;
use backend\models\Subscription;
use backend\models\Transaction;


/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class RazorpayController extends ActiveController
{
    public $modelClass = 'backend\models\TransferRequest';  
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }  
    
    public function actionFetchPayment()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $post = Yii::$app->request->post();
        $headers = getallheaders();
        $payment_id = isset($post['payment_id'])?$post['payment_id']:'';
        $donor_type = isset($post['donor_type'])?$post['donor_type']:'';
        
        if($donor_type == 'Guest')
        {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/payments/'.$payment_id.'');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            
            curl_setopt($ch, CURLOPT_USERPWD, KEY_ID.":". KEY_SECRET);
            
            $result = curl_exec($ch);
            $decode = json_decode($result);
            
            $method= $decode->method;
            
            $modelTransaction = Transaction::find()->where(['payment_id'=>$payment_id])->one();
            $modelTransaction->payment_method =  $method;
            $modelTransaction->payment_charge ='2%';
            $modelTransaction->save(false);
        
           return $decode;
        }
        else {
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
        
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/payments/'.$payment_id.'');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            
            curl_setopt($ch, CURLOPT_USERPWD, KEY_ID.":". KEY_SECRET);
            
            $result = curl_exec($ch);
            $decode = json_decode($result);
            
            
            $method= $decode->method;
            
            $modelTransaction = Transaction::find()->where(['payment_id'=>$payment_id])->one();
            $modelTransaction->payment_method =  $method;
            $modelTransaction->payment_charge ='2%';
            $modelTransaction->save(false);
        
           return $decode;
           
        }
    }
    
    public function actionTransfer(){
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
        $fundraiser_id = isset($post['id'])?$post['id']:'';
        $amount = isset($post['amount'])?$post['amount']:'';
        $user_id = $user_details->id;
        if(!$fundraiser_id){
            $msg = "Id cannot be blank";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $model =TransferRequest::find()->where(['fundraiser_id' => $fundraiser_id])->one();
        $model->status = 1;
        $model->save(false);
        $msg = "Transfered Successfully";
        $success = true;
        $ret = [
            'message' => $msg,
            'statusCode' => 200,
            'success' => $success,
            'transfer_data' => $model
        ];
        return $ret;
    }
    public function actionCancelSubscription(){
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
        $subscriptionId = isset($post['id'])?$post['id']:'';
        $userId = $user_details->id;
        if(!$subscriptionId){
            $msg = "Id cannot be blank";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $modelSubscription = Subscription::find()->where(['status'=>1,'user_id'=>$userId,'id'=>$subscriptionId])->one();
        if(!$modelSubscription){
            Yii::$app->response->statusCode = 404;
            $msg = "Invalid Id.";
            $ret = [
                'message' => $msg,
                'statusCode' => 404,
                'success' => $success
            ];
            return $ret;
        }
        $modelSubscription->status = 0;
        $modelSubscription->save(false);

        $msg = "Subscription Cancelled Successfully";
        $success = true;
        $ret = [
            'message' => $msg,
            'statusCode' => 200,
            'success' => $success
        ];
        return $ret;
    }
    
    public function actionGetContact()
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
        $fundraiser_id = isset($post['id'])?$post['id']:'';
        $user_id = $user_details->id;
        if(!$fundraiser_id){
            $msg = "Id cannot be blank";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        if($fundraiser_id){
            $modelUser = User::find()
            ->leftJoin('fundraiser_scheme','fundraiser_scheme.created_by=user.id')
            ->where(['fundraiser_scheme.id'=>$fundraiser_id])->one();
            $customer_id = $modelUser->customer_id;
            $name = $modelUser->name;
            $email = $modelUser->email;
            $contact_number = $modelUser->phone_number;
            
        }
        
         $url ="https://api.razorpay.com/v1/contacts/";
         $reference_id = rand(1111111111, 9999999999);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, KEY_ID.":". KEY_SECRET);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("content-type: application/json"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"name\":\"$name\",\n  \"email\":\"$email\",\n  \"contact\":\"$contact_number\",\n  \"type\":\"employee\",\n  \"reference_id\":\"$reference_id\",\n  \"notes\":{\n\"notes_key_1\":\"get contact id\",\n\"notes_key_2\":\"get contact id\"\n  }\n}");
        $result = curl_exec($ch);
        $decode = json_decode($result);
        
       return $decode;
    } 
    
    public function actionFundAccount()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        
         $post = Yii::$app->request->post();
         $url ="https://api.razorpay.com/v1/fund_accounts";
         $key_id = "rzp_test_Zsezap5VmtnsTe";
         $key_secret = "nIMUX4AJkyD8ACRuLFhMTtOV";
         
         $contact_id = isset($post['contact_id'])?$post['contact_id']:'';
         $name = isset($post['name'])?$post['name']:'';
         $ifsc = isset($post['ifsc'])?$post['ifsc']:'';;
         $account_number = isset($post['account_number'])?$post['account_number']:'';
         
         
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, KEY_ID.":". KEY_SECRET);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("content-type: application/json"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"contact_id\":\"$contact_id\",\n  \"account_type\":\"bank_account\",\n  \"bank_account\":{\n    \"name\":\"$name\",\n    \"ifsc\":\"$ifsc\",\n    \"account_number\":\"$account_number\"\n  }\n}");
        $result = curl_exec($ch);
        $decode = json_decode($result);
        
        return $decode;
    }
    
    public function actionPayOut()
    {
         
      header('Access-Control-Allow-Origin:*');
	  header('Access-Control-Allow-Headers: *');
	  header("Access-Control-Allow-Headers:Content-Type");
    
         $post = Yii::$app->request->post();
         
         $account_number = 404005261724;
         $amount = isset($post['amount'])?$post['amount']:'';
         $fund_account_id = isset($post['fund_account_id'])?$post['fund_account_id']:'';
         
        $url = "https://api.razorpay.com/v1/payouts";
        $total_amt = $amount * 100 ;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, KEY_ID.":". KEY_SECRET);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("content-type: application/json"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"account_number\": \"$account_number\",\n  \"fund_account_id\": \"$fund_account_id\",\n  \"amount\":$total_amt,\n  \"currency\": \"INR\",\n  \"mode\": \"IMPS\",\n  \"purpose\": \"refund\",\n  \"queue_if_low_balance\": true,\n  \"reference_id\": \"Acme Transaction ID 12345\",\n  \"narration\": \"Acme successfully Transferd\",\n  \"notes\": {\n    \"notes_key_1\":\"Amount Transffered Successfully.\",\n    \"notes_key_2\":\"Amount Transffered Successfully.\"\n  }\n}");
        $result = curl_exec($ch);
        $decode = json_decode($result);
      
        return $decode;
        
    }
    
    public function actionSamplepdf()
    {
     
    $html = "dfgdfdf";


    $conf = [
        'format' => 'A4',
        'mode' => 'utf-8',
        'orientation' => 'P',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 10,
        'margin_bottom' => 0,
        'margin_header' => 10,
        'margin_footer' => 0,
        //....
    ];

    $mpdf = new \Mpdf\Mpdf($conf);
    $mpdf->SetTitle('...');
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->SetAuthor('...');
    $mpdf->SetCreator('...');

    $mpdf->WriteHTML($html);
    $mpdf->Output('filename.pdf', \Mpdf\Output\Destination::INLINE);
    exit(0);
    }
    
   
}


?>