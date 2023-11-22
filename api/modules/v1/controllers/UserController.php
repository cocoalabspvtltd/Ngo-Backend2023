<?php

namespace api\modules\v1\controllers;
use yii;
use yii\rest\ActiveController;
use backend\models\User;
use backend\models\Point;
// use backend\models\Log;
use api\modules\v1\models\Account;
use yii\data\ActiveDataProvider;
use ReallySimpleJWT\Token;
use yii\web\UploadedFile;
use Razorpay\Api\Api;
use backend\models\Donation;
use backend\models\FundraiserScheme;
use yii\helpers\FileHelper;
use Mpdf\Mpdf;
use backend\models\Certificate;
use yii\db\Expression;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class UserController extends ActiveController
{
    public $modelClass = 'backend\models\User';
    public $attribute1;
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }  
    public function  actionRegister()
    {
        header('Access-Control-Allow-Origin: *');
        $post = Yii::$app->request->post();

        $name = isset($post['name'])?$post['name']:'';
        $email = isset($post['email'])?$post['email']:'';
        $phone_number = isset($post['phone_number'])?$post['phone_number']:'';
        $country_code = isset($post['country_code'])?$post['country_code']:'';
        $date_of_birth = isset($post['date_of_birth'])?$post['date_of_birth']:'';
        $message = "";
        $success = false;
        if($name && $email && $phone_number && $country_code && $date_of_birth){
            $modelUser = User::find()->where(['phone_number'=>$phone_number])->andWhere(['status'=>1])->one();
            $modelUserEmail = User::find()->where(['email'=>$email])->andWhere(['status'=>1])->one();
            if($modelUser){
                $message = "Phone number already taken";
                $ret = [
                    'statusCode' => 200,
                    'success' => $success,
                    'message' => $message
                ];
                return $ret;
            }elseif($modelUserEmail){
                $message = "Email already taken";
                $ret = [
                    'statusCode' => 200,
                    'success' => $success,
                    'message' => $message
                ];
                return $ret;
            }else{
                $model = new User;
                $model->name = $name;
                $model->email = $email;
                $model->phone_number = $phone_number;
                $model->country_code = $country_code;
                $model->date_of_birth = date('Y-m-d',strtotime($date_of_birth));
                $model->role = 'campaigner';
                $model->save();

                $modelPoint = Point::find()->where(['status'=>1,'title'=>'register'])->one();
                if($modelPoint && $modelPoint->point){
                    $point = $modelPoint->point;
                    $model->points = $point;
                    $model->save(false);
                }

                $api_key = KEY_ID;
                $api_secret = KEY_SECRET;
                $api = new Api($api_key, $api_secret);
                $customer = $api->customer->create(
                    array(
                        'name' => $name,
                        'email' => $email
                    )
                );
                $model->customer_id = $customer['id'];
                $model->save(false);

                $success = true;
                $message = "Registered Successfully";

                Yii::$app->email->sendSignUpUser($model);
                Yii::$app->email->sendSignUpAdmin($model);

            }
        }else{
            $message = "Name, Email, Country Code, Phone Number, Date Of Birth cannot be blank";
        }
        $ret = [
            'statusCode' => 200,
            'success' => $success,
            'message' => $message
        ];
        return $ret;
    }
    public function  actionSignUp()
    {
        header('Access-Control-Allow-Origin: *');
        $post = Yii::$app->request->post();

        $name = isset($post['name'])?$post['name']:'';
        $email = isset($post['email'])?$post['email']:'';
        $phone_number = isset($post['phone_number'])?$post['phone_number']:'';
        $country_code = isset($post['country_code'])?$post['country_code']:'';
        $date_of_birth = isset($post['date_of_birth'])?$post['date_of_birth']:'';
        $message = "";
        $success = false;
        if($name && $email && $phone_number && $country_code && $date_of_birth){
            $modelUser = User::find()->where(['phone_number'=>$phone_number])->andWhere(['status'=>1])->one();
            $modelUserEmail = User::find()->where(['email'=>$email])->andWhere(['status'=>1])->one();
            if($modelUser){
                $message = "Phone number already taken";
                $ret = [
                    'statusCode' => 200,
                    'success' => $success,
                    'message' => $message
                ];
                return $ret;
            }elseif($modelUserEmail){
                $message = "Email already taken";
                $ret = [
                    'statusCode' => 200,
                    'success' => $success,
                    'message' => $message
                ];
                return $ret;
            }else{
                $model = new User;
                $model->name = $name;
                $model->email = $email;
                $model->phone_number = $phone_number;
                $model->country_code = $country_code;
                $model->date_of_birth = date('Y-m-d',strtotime($date_of_birth));
                $model->role = 'campaigner';
                $model->save();

                $modelPoint = Point::find()->where(['status'=>1,'title'=>'register'])->one();
                if($modelPoint && $modelPoint->point){
                    $point = $modelPoint->point;
                    $model->points = $point;
                    $model->save(false);
                }

                $api_key = KEY_ID;
                $api_secret = KEY_SECRET;
                $api = new Api($api_key, $api_secret);
                $customer = $api->customer->create(
                    array(
                        'name' => $name,
                        'email' => $email
                    )
                );
                $model->customer_id = $customer['id'];
                $model->save(false);

                $success = true;
                $message = "Registered Successfully";

                Yii::$app->email->sendSignUpUser($model);
                Yii::$app->email->sendSignUpAdmin($model);

            }
        }else{
            $message = "Name, Email, Country Code, Phone Number, Date Of Birth cannot be blank";
        }
        $ret = [
            'statusCode' => 200,
            'success' => $success,
            'message' => $message
        ];
        return $ret;
    }
    public function actionSendOtp()
    {
        header('Access-Control-Allow-Origin: *');
        $post = Yii::$app->request->post();
        $msg = "";
        $statusCode = 200;
        $ret = [];
        $success = false;
        $phone_number = isset($post['phone_number'])?$post['phone_number']:'';
        $country_code = isset($post['country_code'])?$post['country_code']:'';
        if(!$phone_number || !$country_code){
            $msg = "Phone number And Country Code cannot be blank";
            $ret = [
                'statusCode' => $statusCode,
                'message' => $msg,
                'success' => $success
            ];
            return $ret;
        }
        $modelUser = User::find()->where(['phone_number'=>$phone_number,'country_code'=>$country_code,'status'=>1])->one();
        if(!$modelUser){
            $msg = "Phone number is not registered";
            $ret = [
                'statusCode' => $statusCode,
                'message' => $msg,
                'success' => $success
            ];
            return $ret;
        }
        $otp = $this->generateOtp($modelUser->id,$phone_number,$country_code);
        if($otp){
            $user_token = isset($otp['user_token'])?$otp['user_token']:'';
            $api_token = isset($otp['api_token'])?$otp['api_token']:'';
            $success = true;
            $ret = [
                'statusCode' => 200,
                'userToken' => $user_token,
                'apiToken' => $api_token,
                'phone' => $phone_number,
                'countryCode' => $country_code,
                'message' => 'OTP sended Successfully',
                'success' => $success
            ];
            return $ret;
        }
        $msg = "somthing went wrong";
        return $ret;
    }
    public function generateOTP($user_id, $phone, $country_code)
    {
        $user_token = substr(uniqid(rand(), true), 4, 4);
        $api_token = substr(uniqid(rand(), true), 1, 15);
        $params = [
            'user_token' => $user_token,
            'api_token' => $api_token,
            'user_id' => $user_id
        ];

        $res = (new Account)->addUserApi($params);

        if ($res) {
            $msg = $user_token." - is your OTP for Crowd Works India Foundation";
            $sendSms = (new Account)->sendSMS(SMS_KEY, SMS_USERID, $country_code, $phone,$msg );

            $data['user_token'] = $user_token;
            $data['api_token'] = $api_token;
            return $data;
        } else {
            return false;
        }
    }
    public function actionResendOtp()
    {
        header('Access-Control-Allow-Origin: *');
        $post = Yii::$app->request->post();
        $msg = "";
        $statusCode = 200;
        $ret = [];
        $success = false;
        $phone_number = isset($post['phone_number'])?$post['phone_number']:'';
        $country_code = isset($post['country_code'])?$post['country_code']:'';
        if(!$phone_number || !$country_code){
            $msg = "Phone number And Country Code cannot be blank";
            $ret = [
                'statusCode' => $statusCode,
                'message' => $msg,
                'success' => $success
            ];
            return $ret;
        }
        $modelUser = User::find()->where(['phone_number'=>$phone_number,'status'=>1,'country_code'=>$country_code])->one();
        if(!$modelUser){
            $msg = "Phone number is not registered";
            $ret = [
                'statusCode' => $statusCode,
                'message' => $msg,
                'success' => $success
            ];
            return $ret;
        }
        $otp = $this->generateOtp($modelUser->id,$phone_number,$country_code);
        if($otp){
            $user_token = isset($otp['user_token'])?$otp['user_token']:'';
            $api_token = isset($otp['api_token'])?$otp['api_token']:'';
            $success = true;
            $ret = [
                'statusCode' => 200,
                'userToken' => $user_token,
                'apiToken' => $api_token,
                'phone' => $phone_number,
                'countryCode' => $country_code,
                'message' => 'OTP sended Successfully',
                'success' => $success
            ];
            return $ret;
        }
        $msg = "somthing went wrong";
        return $ret;
    }
    public function actionVerifyOtp()
    {
        header('Access-Control-Allow-Origin: *');
        $post = Yii::$app->request->post();
        $msg = "";
        $statusCode = 200;
        $ret = [];
        $success = false;
        $otp = isset($post['otp'])?$post['otp']:'';
        $api_token = isset($post['api_token'])?$post['api_token']:'';
        if(!$otp && !$api_token){
            $msg = "OTP cannot be blank";
            $ret = [
                'statusCode' => $statusCode,
                'message' => $msg,
                'success' => $success
            ];
            return $ret;
        }
        $otp_info = (new Account)->getUserAPI($otp, $api_token);
        if(!$otp_info){
            $msg = "Invalid OTP";
            $ret = [
                'statusCode' => $statusCode,
                'message' => $msg,
                'success' => $success
            ];
            return $ret;
        }
        $api_info = (new Account)->getUserApiDetailsById($otp_info['user_id']);
        $userId = $otp_info['user_id'];
        $token = $this->createToken($userId);
        (new Account)->addApiSession($api_info['id'], $token, null);
        $userInfo = (new Account)->findOne($userId);
        $baseUrl = Yii::$app->params['base_path_profile_images'];
        $userDetails = array(
            'baseUrl' => $baseUrl,
            'id' => $userInfo->id,
            'username' => $userInfo->username,
            'name' => $userInfo->name,
            'email' => $userInfo->email,
            'phone_number' => $userInfo->phone_number,
            'password_hash' => $userInfo->password_hash,
            'auth_key' => $userInfo->auth_key,
            'role' => $userInfo->role,
            'status' => $userInfo->status,
            'created_at' => $userInfo->created_at,
            'modified_at' => $userInfo->modified_at,
            'date_of_birth' => $userInfo->date_of_birth,
            'image_url' => $userInfo->image_url,
            'country_code' => $userInfo->country_code,
            'points' => $userInfo->points
        );
        $success = true;
        $ret = [
            'statusCode' => 200,
            'message' => 'Otp verified successfully',
            'userId' => $userId,
            'userDetails' => $userDetails,
            'apiToken' => $token,
            'success' => $success
        ];
        (new Account)->setTokenInactive($otp_info['id']);
        return $ret;
    }
    public function createToken($userId)
    {
        $secret = SECRET_KEY;
        $expiration = time() + 2629746; //time() + 3600; for one month
        $issuer = ISSUER;

        $token = Token::create($userId, $secret, $expiration, $issuer);

        return $token;
    }

    public function actionProfile()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $get = Yii::$app->request->get();
        $msg = "";
        $statusCode = 200;
        $success = false;
        $ret = [];
        $headers = getallheaders();
        
        // $modelLog = new Log;
        // $modelLog->meta = json_encode($headers);
        // $modelLog->save(false);

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
        $token = $this->getBearerToken($api_token);
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
        $baseUrl = Yii::$app->params['base_path_profile_images'];
        $panUrl = Yii::$app->params['base_path_PAN_images'];
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
        $msg = "Profile listed successfully";
        $success = true;
        $ret = [
            'baseUrl' => $baseUrl,
            'panUrl' => $panUrl,
            'statusCode' => 200,
            'message' => $msg,
            'success' => $success,
            'userDetails' => $user_details,
        ];
        return $ret;
    }
    function getBearerToken($headers) 
    {
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
    
     public function actionPancardupload()
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Methods: GET, POST');

        $get = Yii::$app->request->post();
        $msg = "";
        $statusCode = 200;
        $success = false;
        $ret = [];
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
        $token = $this->getBearerToken($api_token);
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
        $images = UploadedFile::getInstanceByName('pancard_image');
        if($images==''){

            $success= false;
            $message="please upload an image";

            $ret = [
                'message' => $message,
                'statusCode' => 401,
                'success' => $success
            ];
            return $ret;
        }
        $filesLocation = Yii::$app->params['upload_path_PAN_images'];
        $user = new User;
        $saveFile = $user->uploadAndSave($images, $filesLocation);
        $image = $user_details->pancard_image?$user_details->pancard_image:"";
        if(isset($saveFile)&&$saveFile!=null){
            $user_details->pancard_image = $saveFile;
        }
        else
        {
            $user_details->pancard_image = $image;
        }
        $user_details->save(false);
        
        $msg = 'PANCard uploaded successfully';
        $success = true;
        $pancard=$user_details->pancard_image;
        $baseUrl = Yii::$app->params['base_path_PAN_images'];
        $ret = [
            'baseUrl' => $baseUrl,
            'statusCode' => $statusCode,
            'success' => $success,
            'message' => $msg,
            'pancard_image' => $pancard,
            'userDetails' => $user_details
        ];
        return $ret;
    }
    
    public function actionUpdateProfile()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $get = Yii::$app->request->post();
        $msg = "";
        $statusCode = 200;
        $success = false;
        $ret = [];
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
        $token = $this->getBearerToken($api_token);
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
        $name = isset($get['name'])?$get['name']:'';
        $email = isset($get['email'])?$get['email']:'';
        $phone_number = isset($get['phone_number'])?$get['phone_number']:'';
        $country_code = isset($get['country_code'])?$get['country_code']:'';
        $date_of_birth = isset($get['date_of_birth'])?$get['date_of_birth']:'';
        if($name){
            $user_details->name = $name;
        }
        if($email){
            $user_details->email = $email;
            $model = User::find()->where(['!=','id',$user_details->id])->andWhere(['email'=>$email])->one();
            if($model){
                $msg = "Email already taken";
                $success = false;
                $ret = [
                    'message' => $msg,
                    'statusCode' => 200,
                    'success' => $success
                ];
                return $ret;
            }
        }
        if($phone_number){
            $user_details->phone_number = $phone_number;
            $model = User::find()->where(['!=','id',$user_details->id])->andWhere(['phone_number'=>$phone_number])->one();
            if($model){
                $msg = "Phone number already taken";
                $success = false;
                $ret = [
                    'message' => $msg,
                    'statusCode' => 200,
                    'success' => $success
                ];
                return $ret;
            }
        }
        if($country_code){
            $user_details->country_code = $country_code;
        }
        if($date_of_birth){
            $user_details->date_of_birth = date('Y-m-d',strtotime($date_of_birth));
        }
        $images = UploadedFile::getInstanceByName('image');
        $filesLocation = Yii::$app->params['upload_path_profile_images'];
        $user = new User;
        $saveFile = $user->uploadAndSave($images, $filesLocation);
        $image = $user_details->image_url?$user_details->image_url:"";
        if(isset($saveFile)&&$saveFile!=null){
            $user_details->image_url = $saveFile;
        }
        else
        {
            $user_details->image_url = $image;
        }
        // if($user_details->customer_id){
        //     $api_key = KEY_ID;
        //     $api_secret = KEY_SECRET;
        //     $api = new Api($api_key, $api_secret);
        //     $customer = $api->customer->fetch($user_details->customer_id)->edit([
        //         'name'  => $user_details->name,
        //         'email' => $user_details->email
        //     ]);
        // }
        $user_details->save(false);
        $msg = 'Profile updated successfully';
        $success = true;
        $user_details =  (new Account)->getCusomerDetailsByAPI($token);
        $baseUrl = Yii::$app->params['base_path_profile_images'];
        $ret = [
            'baseUrl' => $baseUrl,
            'statusCode' => $statusCode,
            'success' => $success,
            'message' => $msg,
            'userDetails' => $user_details
        ];
        return $ret;
    }
    
    public function actionUserProfileDetails()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $get = Yii::$app->request->post();
        
        $user_id= $get['user_id'];

        $user_query= User::find()
        ->where(['id'=> $user_id])->one();
        
        $baseUrl = Yii::$app->params['base_path_PAN_images'];

        $ret = [
            
            'statusCode' => 200,
            'userDetails' => $user_query,
            'baseurl'  => $baseUrl
        ];
        return $ret;
    }
    
    public function actionPaymentHistory()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $get = Yii::$app->request->post();
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
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 401,
                'success' => $success
            ];
            return $ret;
        }
        $userId = $user_details->id;
        
         $donmation_result =Donation::find()
    ->select(['donation.*', 'IF(certificate.donation_id IS NOT NULL, "Certificate Exists", "Certificate Missing") AS status'])
    ->addSelect([
        'show_donor_information' => new Expression('CASE WHEN donation.show_donor_information = 1 THEN "Fundraiser Donation" ELSE "Other Label" END')
    ])
    ->where(['IS NOT', 'donation.fundraiser_id', null])
    ->andWhere(['donation.user_id' => $userId])
    ->joinWith([
        'certificate' => function ($query) {
            // Use a custom configuration for the 'certificate' relation to exclude it from the main array
            $query->asArray(['id', 'other_certificate_fields']); // Specify the fields you want to include
        },
    ])
    ->asArray();
     $results = $donmation_result->all();
    
            $firstDonation = reset($results); 
            $fundraiserId = $firstDonation['fundraiser_id'];
            $Donation_created = reset($results);  
            $created_at = $Donation_created['created_at'];
            
             $show_donor = reset($results);  
            $show_donor_information = $show_donor['show_donor_information'];
            if($results){
            foreach($results as $key=>$items)
            {
             $payment_type = 'Fundraiser';
             $id=$items['fundraiser_id'] ;
             $orgn = FundraiserScheme::find()->select('title')->where(['id' => $id]);
             $res = $orgn->all();
             
             $date =$items['created_at'];
             $newDate = date("d-M", strtotime($date));
             $items['created_at'] = $newDate;
             
             foreach($res as $key=>$value)
             {
            
               $items['fundraiser_id'] = $res[$key];
               
               $hasNextPage = false;
               if(($page*$per_page) < ($donmation_result->count())){
                 
                $hasNextPage = true;
             
                } 
                
             }
          
            }
            
             $payment_ = Donation::find()->where(['fundraiser_id' => null])->andWhere(['user_id'=>$userId]);
             $donate = $payment_->all();
             
             foreach($donate as $key=>$item)
            {
             $payment_type = 'Donate to crowdFund';
             
             
             $certificate = Certificate::find()->where(['donation_id'=> $item->id])->one();
             if($certificate)
             {
             
             $item->status = "Certificate Exists";
             }
             else
             {
             
             $item->status = "Certificate Missing";
             }
            
            $item->show_donor_information = $payment_type;
            
             $date =$item->created_at;
             $newDate = date("d-M", strtotime($date));
             $item->created_at = $newDate;
             
             $hasNextPage = false;
             if(($page*$per_page) < ($payment_->count())){
                 
             $hasNextPage = true;
             
             } 
             
             }
             
            
            $ret = ['statusCode' => 200,'message'=>'Listed successfully','payment_hist' => $results,'donate'=>$donate,'page' => (int) $page,
            'perPage' => (int) $per_page,'hasNextPage' => $hasNextPage,'totalfundraiserCount' => (int) $donmation_result->count(),'totaldonationCount' => (int) $payment_->count()];
        
             return $ret;
            
        }
        
        else
        {
        
            $ret = ['statusCode' => 401,'message' => 'No Records Yet'];
        
            return $ret;
        
        }
    }
    
     public function actionFundraiserPaymentHistory()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $get = Yii::$app->request->post();
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
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 401,
                'success' => $success
            ];
            return $ret;
        }
        $userId = $user_details->id;
        
        
        $donmation_result =Donation::find()
    ->select(['donation.*', 'IF(certificate.donation_id IS NOT NULL, "Certificate Exists", "Certificate Missing") AS status'])
    ->addSelect([
        'show_donor_information' => new Expression('CASE WHEN donation.show_donor_information = 1 THEN "Fundraiser Donation" ELSE "Other Label" END')
    ])
    ->where(['IS NOT', 'donation.fundraiser_id', null])
    ->andWhere(['donation.user_id' => $userId])
    ->joinWith([
        'certificate' => function ($query) {
            // Use a custom configuration for the 'certificate' relation to exclude it from the main array
            $query->asArray(['id', 'other_certificate_fields']); // Specify the fields you want to include
        },
    ])
    ->asArray();
     $results = $donmation_result->all();
    
            $firstDonation = reset($results); 
            $fundraiserId = $firstDonation['fundraiser_id'];
            $Donation_created = reset($results);  
            $created_at = $Donation_created['created_at'];
            
             $show_donor = reset($results);  
            $show_donor_information = $show_donor['show_donor_information'];
       
        if ($results) {
    foreach ($results as $key => $items) {
        unset($items['certificate']);
        $payment_type = 'Fundraiser';
        $id = $items['fundraiser_id'];
        $orgn = FundraiserScheme::find()->select('title')->where(['id' => $id])->scalar();

        // Check if the FundraiserScheme with the given ID exists and has a title
        if ($orgn !== false) {
            // Now you can use $orgn (the title) within your code
            // For example, you can display it or use it in any way you need.

            $date = $items['created_at'];
            $newDate = date("d-M", strtotime($date));
            $items['created_at'] = $newDate;

            // Replace 'fundraiser_id' with 'orgn' (title)
            $items['donated_by'] = $orgn;

            $hasNextPage = false;
            if (($page * $per_page) < ($donmation_result->count())) {
                $hasNextPage = true;
            }
        }

        $items['show_donor_information'] = 'Fundraiser Donation';
    }

    $ret = [
        'statusCode' => 200,
        'message' => 'Listed successfully',
        'payment_hist' => $results,
        'page' => (int) $page,
        'perPage' => (int) $per_page,
        'hasNextPage' => $hasNextPage,
        'totalfundraiserCount' => (int) $donmation_result->count(),
    ];

    return $ret;
}

        
        else
        {
        
            $ret = ['statusCode' => 401,'message' => 'No Records Yet'];
        
            return $ret;
        
        }
    }
    
    public function actionDonationHistory()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $get = Yii::$app->request->post();
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
            $success = false;
            $ret = [
                'message' => $msg,
                'statusCode' => 401,
                'success' => $success
            ];
            return $ret;
        }
        $userId = $user_details->id;
        
        $payment_hist = Donation::find()->where(['fundraiser_id' => null])->andWhere(['user_id'=>$userId])->with('certificate');
        $results = $payment_hist->all();
        if($results){
             foreach($results as $key=>$item)
            {
             $payment_type = 'Donate to crowdFund';
             
             $certificate = Certificate::find()->where(['donation_id'=> $item->id])->one();
             if($certificate)
             {
             
             $item->status = "Certificate Exists";
             }
             else{
                 
                $item->status = "Certificate Missing"; 
             }
            $item->show_donor_information = $payment_type;
            
             $date =$item->created_at;
             $newDate = date("d-M", strtotime($date));
             $item->created_at = $newDate;
             
             $hasNextPage = false;
             if(($page*$per_page) < ($payment_hist->count())){
                 
             $hasNextPage = true;
             
             } 
             
             }
            
             
            $ret = ['statusCode' => 200,'message'=>'Listed successfully','donate' => $results,'page' => (int) $page,
            'perPage' => (int) $per_page,'hasNextPage' => $hasNextPage,'totaldonationCount' => (int) $payment_hist->count()];
        
             return $ret;
            
        }
        
        else
        {
        
            $ret = ['statusCode' => 401,'message' => 'No Records Yet'];
        
            return $ret;
        
        }
    }
    
    
    public function actionGeneratePdf()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $post = Yii::$app->request->post();
       
        $data = [
            'id' => $post['user_id'],
            'donation_id' => $post['donation_id']
        ];

        $pdfFilePath = $this->generatePdf($data);
        
        $file_path = "https://crowdworksindia.org";

        return [
            'success' => true,
            'pdf_path' => $pdfFilePath,
            'file_path' =>  $file_path
        ];
    }

    private function generatePdf($data)
    {
        
        $form_data = Certificate::find()->where(['user_id'=> $data['id']])
        ->andWhere(['donation_id' => $data['donation_id']])->one();
        $date = date("d-m-Y");
        $length = 5;
        $string = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,$length);
        $certificate_number = $string.$data['id'];
        $mpdf = new Mpdf();

        // Add content to the PDF (replace with your actual PDF content generation)
        $pdfContent = '<html lang="en">
                <head>
                <!-- html2pdf CDN-->
                <script src=
                "https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js">
                </script>
                
                <style>
                 .container {
                 position: fixed;
                 top: 16%;
                 left: 25%;
                margin-top: -90px;
                 margin-left: -100px;
                border-radius: 7px;
                }
                
                 .card {
                 box-sizing: content-box;
                 width: 100%;
                 height: 750px;
                 padding: 40px;
                 border: 1px solid black;
                 font-style: sans-serif;
                 margin:auto;
                 margin-top:20px;
                 background-color: #f0f0f0;
                 background-image :url("https://crowdworksindia.org/test/common/uploads/80GForm/80g Crowd Works.jpg");
                 background-repeat: no-repeat;
                 background-size: cover;
                
                 }
                
                 .text-pdf{
                     margin-top:23%;
                     font-size: 19px;
                     
                 }
                 #button {
                 background-color: #4caf50;
                 border-radius: 5px;
                 margin-left: 116px;
                 margin-bottom: 2px;
                 color: white;
                }
                
                 h2 {
                 text-align: center;
                 color: #24650b;
                 }
                </style>
                </head>
                
                <body>
                 <div>
                 <div class="card" id="makepdf">
                <p class="text-pdf">
                <h5>Date: <b>'.$date.'</b></h5>
                <h5>Certificate No: <b>'.$certificate_number.'</b></h5>
                <h5>Dear,</h5>
                <p class="text-size">This is to Confirm that Crowd Works India Foundation recived a total amount of Rs.<u><b>'. $form_data->amount.'</b></u> from <u><b>'. $form_data->name.'</b></u> PAN no. <u><b>'. $form_data->pan_number.'</b></u> as per the details given </p><br>
                <h5>Date: <b>'. $date.'</b></h3>
                <h5>Amount: <b>'. $form_data->amount.'</b></h3>
                <h5>Invoice no: <b>AVd32425</b></h3><br>
                <p class="text-size" style="font-size:15px;">Heartfelt gratitude for your generous donation. Your support will go a long way in helping us achieve our mission.</p><br>
                <p class="text-size" style="font-size:15px;">This is a computer-generated receipt and does not require a sign. <b>Crowd Works India Foundation</b> PAN no. : <b>AACTC7179C</b></p>
                </p>
                </div>
                </div>
                
                <script>
                 let button = document.getElementById("button");
                let makepdf = document.getElementById("makepdf");
                
                button.addEventListener("click", function () {
                html2pdf().from(makepdf).save();
                });
                </script>
                </body>
                </html>';

        // Write content to the PDF
        $mpdf->WriteHTML($pdfContent);
        
        $tempFilePath = Yii::getAlias('@uploads/runtime/pdf/'). 'report_' . time() . '.pdf';
        
        FileHelper::createDirectory(dirname($tempFilePath));
        $mpdf->Output($tempFilePath, \Mpdf\Output\Destination::FILE);

        return $tempFilePath;
    }
    
     public function actionGenerateReceipt()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $post = Yii::$app->request->post();
       
        $data = [
            'id' => $post['fundraiser_id']
        ];

        $pdfFilePath = $this->generateReceipt($data);
        
        $file_path = "https://crowdworksindia.org";

        return [
            'success' => true,
            'pdf_path' => $pdfFilePath,
            'file_path' =>  $file_path
        ];
    }

      private function generateReceipt($data)
      {
         
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        
        $form_data = FundraiserScheme::find()->where(['id'=> $data['id']])->one();
        if($form_data->pricing_id == 1)
        {
            $pricing = '0%';
        }
        else if($form_data->pricing_id == 2)
        {
            $pricing = '5%';
        }
        else if($form_data->pricing_id == 3)
        {
            $pricing = '8%';
        }
        $date = date("d-m-Y");
        $length = 5;
        $string = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,$length);
        $certificate_number = $string.$data['id'];
        $mpdf = new Mpdf();

        // Add content to the PDF (replace with your actual PDF content generation)
        $pdfContent = '<html lang="en">
                        <head>
                        <!-- html2pdf CDN-->
                         <script src=
                        "https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js">
                         </script>
                        
                        <style>
                       
                        
                        .card {
                        box-sizing: content-box;
                        width: 600px;
                        height: 750px;
                        padding: 40px;
                        border: 1px solid black;
                        font-style: sans-serif;
                        background-color: #f0f0f0;
                        margin:auto;
                        margin-top:20px;
                        background-image :url("https://crowdworksindia.org/test/common/uploads/CrowdWorksIndiaReceipt/newreciept.jpg");
                         background-repeat: no-repeat;
                          background-size: cover;
                         }
                        
                        .text-pdf{
                            margin-top:-8%;
                            text-align: justify;
                            line-height: 31px;
                            padding: 50px;
                        }
                        
                        .invoice-to
                        {
                            margin-top: 20%;
                            margin-left: 0px;
                        }
                        
                        .invoice-data
                        {
                            margin-top: 5%;
                            float: right;
                            margin-right: 70px;
                        }
                        
                        .invoice-date
                        {
                            margin-top: 30%;
                            float: right;
                            margin-left: 45px; 
                            
                        }
                        
                        
                        #button {
                         background-color: #4caf50;
                         border-radius: 5px;
                         margin-left: 225px;
                        margin-bottom: 0px;
                         color: white;
                        }
                        
                        h2 {
                         text-align: center;
                         color: #24650b;
                         }
                         </style>
                        </head>
                        
                        <body>
                        <div class="container">
                        <div class="card" id="makepdf">
                        <p class="invoice-date">Date:'.$date.'</p><br><br>
                        <p class="text-pdf">
                        With sincere appreciation and heartfelt gratitude, we write to express our deepest thanks for your recent donation of <u><b>Rs.'.$form_data->fund_raised.'</b></u> to Crowd Works India Foundation. Your incredible act of kindness has touched our hearts and will have a lasting impact on our mission.
                        We are truly humbled by your trust and belief in our work. Your donation not only provides financial support but also serves as a testament to the power of compassion and unity.Once again, we extend our deepest gratitude for your remarkable generosity. If you have any further questions or would like to stay updated on our progress, please do not hesitate to contact us. We would be honored to connect with you and provide any information you may need.
                        <br>With utmost appreciation<br>'.$pricing.' is deducted from your Raised Amount
                         </p>
                        </div>
                        </div>
                        
                        <script>
                        let button = document.getElementById("button");
                        let makepdf = document.getElementById("makepdf");
                        
                         button.addEventListener("click", function () {
                        html2pdf().from(makepdf).save();
                        });
                         </script>
                        </body>
                        </html>';

        // Write content to the PDF
        $mpdf->WriteHTML($pdfContent);
        
        $tempFilePath = Yii::getAlias('@uploads/runtime/receipt/'). 'report_' . time() . '.pdf';
        
        FileHelper::createDirectory(dirname($tempFilePath));
        $mpdf->Output($tempFilePath, \Mpdf\Output\Destination::FILE);

        return $tempFilePath;
    }

}


