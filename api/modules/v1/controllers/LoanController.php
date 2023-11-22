<?php

namespace api\modules\v1\controllers;
use yii;
use yii\rest\ActiveController;
use backend\models\Loan;
use backend\models\LoanDonation;
use backend\models\User;
use  api\modules\v1\models\Account;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;
use Razorpay\Api\Api;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class LoanController extends ActiveController
{
    public $modelClass = 'backend\models\Loan';   
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }     
    public function  actionCreateLoan(){
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
        $title = isset($post['title'])?$post['title']:'';
        $purpose = isset($post['purpose'])?$post['purpose']:'';
        $amount = isset($post['amount'])?$post['amount']:'';
        $location = isset($post['location'])?$post['location']:'';
        $description = isset($post['description'])?$post['description']:'';
        $no_of_days = isset($post['no_of_days'])?$post['no_of_days']:'';
        if(!$title && !$purpose && !$amount && !$location && !$description && !$no_of_days){
            $msg = "Title, Purpose, Amount, Location, Description and No. of days cannot be blank";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $date = date('Y-m-d');
        $closing_date = date('Y-m-d', strtotime($date. ' + '.$no_of_days.' days'));
        $model = new Loan;
        $model->title = $title;
        $model->purpose = $purpose;
        $model->amount = $amount;
        $model->location = $location;
        $model->description = $description;
        $model->created_by = $user_details->id;
        $model->closing_date = $closing_date;
        $main_image = UploadedFile::getInstanceByName('image_url');
        if($main_image && !empty($main_image)){
            $imageLocation = Yii::$app->params['upload_path_loan_images'];
            $modelUser = new User;
            $saveImage = $modelUser->uploadAndSave($main_image,$imageLocation);
            if($saveImage){
                $model->image_url = $saveImage;
            }
        }
        $model->save(false);

        $customer_id = $user_details->customer_id;
        // if($customer_id){
        //     $api_key = KEY_ID;
        //     $api_secret = KEY_SECRET;
        //     $api = new Api($api_key, $api_secret);
        //     $virtualAccount = $api->virtualAccount->create(
        //         array(
        //             'receiver_types' => array(
        //                 'bank_account'
        //             ),
        //             'description' => 'Fundraiser title = '.$title,
        //             'customer_id' => $customer_id,
        //             'notes' => array(
        //                 'loan_id' => $model->id
        //             )
        //         )
        //     );
        //     $model->virtual_account_id = $virtualAccount['id'];
        //     $model->save(false);
        //     $virtualAccount = $api->virtualAccount->fetch($virtualAccount['id']);
        //     if($virtualAccount){
        //         $model->virtual_account_name = $virtualAccount['name'];
        //         $model->virtual_account_number = $virtualAccount['receivers']['0']['account_number'];
        //         $model->virtual_account_ifsc = $virtualAccount['receivers']['0']['ifsc'];
        //         $model->virtual_account_type = 'Current';
        //         $model->save(false);
        //     }
        // }

        $msg = "Loan Created Successfully";
        $success = true;
        $ret = [
            'message' => $msg,
            'statusCode' => 200,
            'success' => $success
        ];
        return $ret;
    }
    public function actionList(){
        header('Access-Control-Allow-Origin: *');
        $baseUrl = Yii::$app->params['base_path_loan_images'];
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        $keyword = isset($get['keyword'])?$get['keyword']:'';
        $amount = isset($get['amount'])?$get['amount']:'';
        $query = Loan::find()->where(['status'=>1,'is_approved'=>1]);
        if($keyword){
            $query->andWhere(['like','title',$keyword]);
        }
        if($amount){
            if($amount == 'asc'){
                $query->orderBy(['amount'=>SORT_ASC]);
            }
            if($amount == 'desc'){
                $query->orderBy(['amount'=>SORT_DESC]);
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
        $webBaseUrl = 'https://www.cocoalabs.in/ngo-web/ngo/loan-details/';
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
    public function actionDetail(){
        header('Access-Control-Allow-Origin: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $get = Yii::$app->request->get();
        $loan_id = isset($get['loan_id'])?$get['loan_id']:'';
        if(!$loan_id){
            $msg = 'Loan Id cannot be blank';
            $ret = [
                'statusCode' => $statusCode,
                'success' => $success,
                'message' => $msg
            ];
            return $ret;
        }
        $model = Loan::find()
        ->where(['status'=>1,'id'=>$loan_id,'is_approved'=>1])->one();
        if($model){
            $fund_raised = LoanDonation::find()->where(['status'=>1,'loan_id'=>$loan_id])->sum('amount');
            $baseUrl = Yii::$app->params['base_path_loan_images'];
            $webBaseUrl = 'https://www.cocoalabs.in/ngo-web/ngo/loan-details/';
            $ret = [
                'statusCode' => 200,
                'baseUrl' => $baseUrl,
                'webBaseUrl' => $webBaseUrl,
                'loanDetails' => $model,
                'fund_raised' => (double) $fund_raised,
                'message' => 'Listed Successfully',
                'success' => true
            ];
            return $ret;
        }else{
            $msg = 'Loan is Not Approved';
            $ret = [
                'statusCode' => $statusCode,
                'success' => $success,
                'message' => $msg
            ];
            return $ret;
        }
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
        $loan_id = isset($post['loan_id'])?$post['loan_id']:'';
        $amount = isset($post['amount'])?$post['amount']:'';
        $transaction_id = isset($post['transaction_id'])?$post['transaction_id']:'';
        if($loan_id && $amount){
            $modelLoan = Loan::find()->where(['status'=>1,'id'=>$loan_id])->one();
            if($modelLoan){
                $modelDonation = new LoanDonation;
                $modelDonation->loan_id = $loan_id;
                $modelDonation->user_id = $userId;
                $modelDonation->amount = $amount;
                $modelDonation->transaction_id = $transaction_id;
                $modelDonation->save(false);
                $success = true;
                $msg = "Donation received successfully";
            }else{
                $msg = 'Invalid Loan Id';
            }
        }else{
            $msg = 'Loan Id And Amount cannot be blank';
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $ret = [
            'statusCode' => $statusCode,
            'success' => $success,
            'message' => $msg
        ];
        return $ret;
    }
    public function actionMyLoans(){
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
        $query = Loan::find()->where(['status'=>1,'created_by'=>$user_details->id]);
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
        $baseUrl = Yii::$app->params['base_path_loan_images'];
        $webBaseUrl = 'https://www.cocoalabs.in/ngo-web/ngo/loan-details/';
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
    public function actionMyLends(){
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
        // $query = LoanDonation::find()
        // ->leftJoin('loan','loan.id=loan_donation.loan_id')
        // ->where(['loan_donation.status'=>1,'user_id'=>$user_details->id])
        // ->select('loan_donation.*,loan.title,loan.purpose,loan.amount as loan_amount,loan.image_url as image_url');
        $query = LoanDonation::find()
        ->where(['loan_donation.status'=>1,'user_id'=>$user_details->id])
        ->select('loan_donation.*');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [$page, $per_page]
            ]
        ]);
        $list = [];
        if($dataProvider->getModels()){
            foreach($dataProvider->getModels() as $model){
                $modelLoan = Loan::find()->where(['id'=>$model->loan_id])->one();
                $list[] = [
                    'id' => $model->id,
                    'user_id' => $model->user_id,
                    'loan_id' => $model->loan_id,
                    'amount' => $model->amount,
                    'title' => $modelLoan->title,
                    'purpose' => $modelLoan->purpose,
                    'loan_amount' => $modelLoan->amount,
                    'image_url' => $modelLoan->image_url
                ];
            }
        }
        $hasNextPage = false;
        if(($page*$per_page) < ($query->count())){
            $hasNextPage = true;
        }
        $baseUrl = Yii::$app->params['base_path_loan_images'];
        $webBaseUrl = 'https://www.cocoalabs.in/ngo-web/ngo/loan-details/';
        $ret = [
            'statusCode' => 200,
            'baseUrl' => $baseUrl,
            'webBaseUrl' => $webBaseUrl,
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
    public function actionUpdateLoan(){
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
        $loan_id = isset($post['loan_id'])?$post['loan_id']:'';
        if(!$loan_id){
            $msg = "Loan id cannot be blank.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $model = Loan::find()->where(['id'=>$loan_id,'status'=>1])->one();
        if(!$model){
            $msg = "Invalid loan id.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
        $title = isset($post['title'])?$post['title']:$model->title;
        $purpose = isset($post['purpose'])?$post['purpose']:$model->purpose;
        $amount = isset($post['amount'])?$post['amount']:$model->amount;
        $location = isset($post['location'])?$post['location']:$model->location;
        $description = isset($post['description'])?$post['description']:$model->description;
        $no_of_days = isset($post['no_of_days'])?$post['no_of_days']:'';
        $oldImage = $model->image_url;

        $model->title = $title;
        $model->purpose = $purpose;
        $model->amount = $amount;
        $model->location = $location;
        $model->description = $description;
        $model->created_by = $user_details->id;
        if($no_of_days){
            $date = date('Y-m-d');
            $closing_date = date('Y-m-d', strtotime($date. ' + '.$no_of_days.' days'));
            $model->closing_date = $closing_date;
        }
        $main_image = UploadedFile::getInstanceByName('image_url');
        if($main_image && !empty($main_image)){
            $imageLocation = Yii::$app->params['upload_path_loan_images'];
            $modelUser = new User;
            $saveImage = $modelUser->uploadAndSave($main_image,$imageLocation);
            if($saveImage){
                $model->image_url = $saveImage;
            }
        }else{
            $model->image_url = $oldImage;
        }
        $model->save(false);
        $msg = "Loan Updated Successfully";
        $success = true;
        $ret = [
            'message' => $msg,
            'statusCode' => 200,
            'success' => $success
        ];
        return $ret;
    }
}