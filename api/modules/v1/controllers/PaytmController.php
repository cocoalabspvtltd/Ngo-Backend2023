<?php
namespace api\modules\v1\controllers;


header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Credentials", "true");
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: GET, POST');
// header("Access-Control-Allow-Methods:POST,GET,OPTIONS");
header("Access-Control-Allow-Headers:Content-Type");


use backend\models\VanAccount;
use backend\models\Transaction;
use backend\models\User;
use backend\models\FundraiserScheme;
use yii;
use common\components\PaytmChecksums;
use phpDocumentor\Reflection\PseudoTypes\True_;
use yii\base\Response;
use yii\rest\ActiveController;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;


class PaytmController extends ActiveController 

{
/*
 * @inheritdoc
 */
 

public function behaviors()
{
    return ArrayHelper::merge([
        [
            'class' => Cors::className(),
        ],
    ], parent::behaviors());
}
 
    public $modelClass = 'backend\models\VanAccount';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function  actionVaCreate()
    {
      header('Access-Control-Allow-Origin:*');
      header('Access-Control-Allow-Methods: GET, POST');
	  header('Access-Control-Allow-Headers: *');


        /*
    * import checksum generation utility
    * You can get this utility from https://developer.paytm.com/docs/checksum/
    */

        $get = Yii::$app->request->post();
        $user_id = $get['user_id'];

        $paytmParams = array();

        $reference_id = rand(1111111111, 9999999999);
        $requestId = rand() . '' . date("His");
        // print_r('test data');exit;
        $paytmParams["body"] = array(
            "requestId"  => $requestId,
            "mid"        => "CROWDW68252167261528",
            "vanDetails" => array(
                array(
                    "identificationNo" => $reference_id,
                    "purpose" => "Payment",
                    "merchantPrefix"  => "CWIF",
                    "customerDetails" => array(
                        array(
                            "customerEmail"  => "albin@gmail.com",
                            "customerMobile" => "9875263210",
                            "customerName"   => "test user"
                        )
                    ),
                )
            )
        );
        /*
                    * Generate checksum by parameters we have in body
                    * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
                    */
        $checksum = PaytmChecksums::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "g%vMkDeu7Kt_LHX0");
        $paytmParams["head"] = array(
            // "version"          => "v1",
            //"requestTimestamp" => "1602159438",
            "channelId"        => "WAP",
            "tokenType"        => "CHECKSUM",
            "token"           => $checksum
        );

        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

        /* for Staging */
        //$url = "https://securegw-stage.paytm.in/vanproxy/api/v1/van?mid=CROWDW96793031223702";
        /* for Production */
        // $url = "https://securegw.paytm.in/vanproxy/api/v1/van?mid=YOUR_MID_HERE";
         $url = "https://securegw.paytm.in/vanproxy/api/v1/van?mid=CROWDW68252167261528";
		 
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        $response = curl_exec($ch);
        $response = json_decode($response);
        $Van_details = VanAccount::find()->where(['user_id' => $user_id])->one();
        if ($Van_details) {
            return $Van_details;
        }
        $vanDetails = $response->body->vanDetails['0'];
        $van = new VanAccount();
        $van->user_id = $user_id;
        $van->van = $vanDetails->van;
        $van->identificationNo = $vanDetails->identificationNo;
        $van->active = $vanDetails->active;
        $van->ifscCode = $vanDetails->ifscCode;
        $van->save();
        return VanAccount::find()->where(['user_id' => $user_id])->one();
    }

    /**
     * Initiate transaction
     * 
     * @param 
     */
    public function actionInitiate()
    {
       header('Access-Control-Allow-Origin:*');
       header('Access-Control-Allow-Headers: *');
       header('Access-Control-Allow-Methods: GET, POST');

        $get = Yii::$app->request->post();

        if (!empty($get['user_id'])){
        $user_details= User::find()->where(['id'=>$get['user_id']])->one();
        $amount = $get['amount'];
        $name=$user_details->name;
        $email=$user_details->email;
        $phone=$user_details->phone_number;
        $payment_type= $get['payment_type'];

        }
        else
        {
            $name = $get['name'];
            $email = $get['email'];
            $phone = $get['phone'];
            $amount= $get['amount'];
            $payment_type= $get['payment_type'];
        }


        /*
* import checksum generation utility
* You can get this utility from https://developer.paytm.com/docs/checksum/
*/

        $paytmParams = array();
        $order_id = "ORDERID_" . rand(11111, 99999);
        $paytmParams["body"] = array(
            "requestType"   => "Payment",
            "mid"           => "CROWDW68252167261528",
            "websiteName"   => "DEFAULT",
            "orderId"       => $order_id,
            "callbackUrl"   => "https://securegw.paytm.in/theia/paytmCallback?ORDER_ID=" . $order_id . "",
            "txnAmount"     => array(
                "value"     => "1.00",
                "currency"  => "INR",
            ),
            "userInfo"      => array(
                "custId"    => "CUST_001",
                'name'=>$name,
                'mobile'=>$phone,
                'email'=>$email,
            ),
        );

        /*
* Generate checksum by parameters we have in body
* Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
*/
        $checksum = PaytmChecksums::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "g%vMkDeu7Kt_LHX0");

        $paytmParams["head"] = array(
            "signature"    => $checksum
        );

        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

        /* for Staging */
        //$url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=CROWDW96793031223702&orderId=".$order_id."";

        /* for Production */
         $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=CROWDW68252167261528&orderId=".$order_id."";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        $response = curl_exec($ch);
        $response = json_decode($response);
        $status = $response->body->resultInfo->resultMsg;
        $response->mid = "CROWDW68252167261528";
        $response->amount = $amount;
        $response->name = $name;
        $response->email = $email;
        $response->phone = $phone;
        $response->payment_type = $payment_type;
        $response->order_id = $order_id;
        $response->checksum = $checksum;
        $response->status = $status;  
        return $response;
		
    }
    
    public function actionFetchPayment(){
        
       header('Access-Control-Allow-Origin:*');
       header('Access-Control-Allow-Headers: *');
       header('Access-Control-Allow-Methods: GET, POST');

        $get = Yii::$app->request->post();
        
        $token= $get ['token'];
        $order_id= $get['order_id'];
        
        $paytmParams = array();
        $paytmParams["head"] = array(
        "tokenType" => "TXN_TOKEN",
        'token'     => $token
        );
        $paytmParams["body"] = array(
        "mid" => "CROWDW68252167261528",
        "orderId" => $order_id,
        "returnToken"  => "true"
        );
        /* prepare JSON string for request */
        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
        /* for Staging */
        //$url = "https://securegw-stage.paytm.in/theia/api/v2/fetchPaymentOptions?mid=CROWDW96793031223702&orderId=$order_id";

        /* for Production */
        $url = "https:securegw.paytm.in/theia/api/v2/fetchPaymentOptions?mid=YOUR_MID_HERE&orderId=ORDERID_98765";


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
        $response = curl_exec($ch);
        $response = json_decode($response);
        return $response;         


    }
	
public function actionFundraiserInitiate()
    {

      header('Access-Control-Allow-Origin:*');
      header('Access-Control-Allow-Headers: *');
      header('Access-Control-Allow-Methods: GET, POST');

        $get = Yii::$app->request->post();
        $name = $get['name'];
        $email = $get['email'];
        $phone = $get['phone'];
        $amount = $get['amount'];
        /*
* import checksum generation utility
* You can get this utility from https://developer.paytm.com/docs/checksum/
*/

        $paytmParams = array();
        $order_id = "ORDERID_" . rand(11111, 99999);
        $paytmParams["body"] = array(
            "requestType"   => "Payment",
            "mid"           => "CROWDW96793031223702",
            "websiteName"   => "DEFAULT",
            "orderId"       => $order_id,
            "callbackUrl"   => "https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=" . $order_id . "",
            "txnAmount"     => array(
                "value"     =>  "1",
                "currency"  => "INR",
            ),
            "userInfo"      => array(
                "custId"    => "CUST_001",
                'name'=>$name,
                'mobile'=>$phone,
                'email'=>$email,
            ),
        );

        /*
* Generate checksum by parameters we have in body
* Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
*/
        $checksum = PaytmChecksums::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "_b4oz3#i90XR49q3");

        $paytmParams["head"] = array(
            "signature"    => $checksum
        );

        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

        /* for Staging */
        $url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=CROWDW96793031223702&orderId=".$order_id."";

        /* for Production */
        // $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=CROWDW68252167261528&orderId=".$order_id."";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        $response = curl_exec($ch);
        $response = json_decode($response);
        $response->mid = "CROWDW96793031223702";
        $response->amount = $amount;
        $response->order_id = $order_id;
        return $response;
		
    }
	
    public function actionTransactionstatus()
	{
		
     header('Access-Control-Allow-Origin:*');
     header('Access-Control-Allow-Headers: *');
     header('Access-Control-Allow-Methods: GET, POST');
		
      /**
      * import checksum generation utility
      * You can get this utility from https://developer.paytm.com/docs/checksum/
      */
     
      $paytmParams = array();
	 
	  $post = Yii::$app->request->post();
	  
      $order_id = $post['order_id'];

       $name= $post['name'];
       $email= $post['email'];
       $phone= $post['phone'];
       $payment_type= $post['payment_type'];
    
      
      $paytmParams["body"] = array(
     
   
       "mid" => "CROWDW68252167261528",
	   
       "orderId" => $order_id,
      );
      
		/**
		* Generate checksum by parameters we have in body
		* Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
		*/
      $checksum = PaytmChecksums::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "g%vMkDeu7Kt_LHX0");

      /* head parameters */
      $paytmParams["head"] = array(

      /* put generated checksum value here */
      "signature"	=> $checksum
      );

      /* prepare JSON string for request */
      $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

      /* for Staging */
      //$url = "https://securegw-stage.paytm.in/v3/order/status";

      /* for Production */
      $url = "https://securegw.paytm.in/v3/order/status";

      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));  
      $response = curl_exec($ch);
	  $response = json_decode($response);
	  $response->mid = "CROWDW68252167261528";
      $response->order_id = $order_id;
      
      $paymentmode = $response->body->paymentMode;
      $TXNAMOUNT = $response->body->txnAmount;
      $TXNID = $response->body->txnId;
      $STATUS = $response->body->resultInfo->resultStatus;
      $TXNDATE = $response->body->txnDate;
      
      $model_transaction=  new Transaction();
      $model_transaction->txn_id =$TXNID;
      $model_transaction->donor_name =$name;
      $model_transaction->donor_email =$email;
      $model_transaction->donor_phone =$phone;
      $model_transaction->payment_type =$payment_type;
      $model_transaction->payment_status=$STATUS;
      $model_transaction->payment_method=$paymentmode;
      $model_transaction->amount= $TXNAMOUNT;
      $model_transaction->txn_date= $TXNDATE;
      $model_transaction->save();

      return $response;
	  
	}

    public function actionPayoutapi()
    {
     header('Access-Control-Allow-Origin:*');
     header('Access-Control-Allow-Headers: *');
     header('Access-Control-Allow-Methods: GET, POST');
      
	  $post = Yii::$app->request->post();
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
      $data = <<<JSON
        {
         "account_number":"2323230075926175",
         "fund_account_id":"$fund_account_id",
         "amount" : "$total_amt",
         "currency" : "INR",
         "mode" : "NEFT",
         "purpose" : "payout",
         "narration": "",
         "notes": {
           "notes_key_1":"Amount Transffered Successfully"
          }
        }  
        JSON;
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      $result = curl_exec($ch);
      $decode = json_decode($result);  
      
      $ret = [
            'result' => $decode
            ];
	  return $ret;
    }
    
    public function actionFundraiserTransactionStatus()
	{
		
       header('Access-Control-Allow-Origin:*');
       header('Access-Control-Allow-Headers: *');
       header('Access-Control-Allow-Methods: GET, POST');
		
      /**
      * import checksum generation utility
      * You can get this utility from https://developer.paytm.com/docs/checksum/
      */
     
      $paytmParams = array();
	 
	  $post = Yii::$app->request->post();
	  
      $order_id = $post['order_id'];

      $fundraiser_id = $post['fundraiser_id'];
      
      $paytmParams["body"] = array(
     
   
       "mid" => "CROWDW96793031223702",
	   
       "orderId" => $order_id,
      );
      
		/**
		* Generate checksum by parameters we have in body
		* Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
		*/
      $checksum = PaytmChecksums::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "_b4oz3#i90XR49q3");

      /* head parameters */
      $paytmParams["head"] = array(

      /* put generated checksum value here */
      "signature"	=> $checksum
      );

      /* prepare JSON string for request */
      $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

      /* for Staging */
      $url = "https://securegw-stage.paytm.in/v3/order/status";

      /* for Production */
      //$url = "https://securegw.paytm.in/v3/order/status";

      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));  
      $response = curl_exec($ch);
	  $response = json_decode($response);
	  $response->mid = "CROWDW96793031223702";
      $response->order_id = $order_id;
      
      $paymentmode = $response->body->paymentMode;
      $TXNAMOUNT = $response->body->txnAmount;
      $TXNID = $response->body->txnId;
      $STATUS = $response->body->resultInfo->resultStatus;
      $TXNDATE = $response->body->txnDate;
      
      $model_transaction=  new Transaction();
      $model_transaction->txn_id =$TXNID;
      $model_transaction->fundraiser_id =$fundraiser_id;
      $model_transaction->payment_status=$STATUS;
      $model_transaction->payment_method=$paymentmode;
      $model_transaction->amount= $TXNAMOUNT;
      $model_transaction->txn_date= $TXNDATE;
      $model_transaction->save();

      if($model_transaction->save()){

        $query = FundraiserScheme::find()
        ->where(['id'=>$fundraiser_id])->one();

        $query->fund_raised +=$TXNAMOUNT;
        $query->save(false);

        $goal_amt=$query->fund_raised;
        $fund_raised= $query->fund_raised;

        $reduced_amt= $goal_amt - $fund_raised;

      }

      $response->reduced_amount = $reduced_amt;
      return $response;
	  
	}
	
}
