<?php

namespace api\modules\v1\controllers;
use yii;
use yii\rest\ActiveController;
use backend\models\ReportedFundraiserScheme;
use api\modules\v1\models\Account;
use yii\data\ActiveDataProvider;
use backend\models\FundraiserScheme;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class ReportIssueController extends ActiveController
{
    public $modelClass = 'backend\models\ReportedFundraiserScheme';    
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }  
    public function  actionAdd(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $post = Yii::$app->request->post();
        $name = isset($post['name'])?$post['name']:'';
        $email = isset($post['email'])?$post['email']:'';
        $phone = isset($post['phone'])?$post['phone']:'';
        $description = isset($post['description'])?$post['description']:'';
        $fundraiser_scheme_id = isset($post['fundraiser_scheme_id'])?$post['fundraiser_scheme_id']:'';
        if($description && $fundraiser_scheme_id){
            $headers = getallheaders();
            $api_token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');
            $model = new ReportedFundraiserScheme;
            if($api_token){
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
                    $success = false;
                    Yii::$app->response->statusCode = 401;
                    $msg = "Somthing went wrong.";
                    $ret = [
                        'message' => $msg,
                        'statusCode' => 401,
                        'success' => $success
                    ];
                    return $ret;
                }
                $name = $user_details->name;
                $email = $user_details->email;
                $phone = $user_details->phone_number;
                $model->user_id = $user_details->id;
            }
            $model->name = $name;
            $model->email = $email;
            $model->phone_number = $phone;
            $model->description = $description;
            $model->fundraiser_scheme_id = $fundraiser_scheme_id;
            $model->save(false);
            $ret = [
                'statusCode' => 200,
                'message' => 'Reported Successfully',
                'success' => true
            ];
        }else{
            $ret = [
                'statusCode' => 200,
                'message' => 'Description and Fundraiser scheme id cannot be blank',
                'success' => false
            ];
        }
        return $ret;
    }

    public function actionCancelFundraiser(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $post = Yii::$app->request->post();
        $fundraiser_scheme_id = isset($post['fundraiser_scheme_id'])?$post['fundraiser_scheme_id']:'';
        $reason = isset($post['reason'])?$post['reason']:'';
        if(!$fundraiser_scheme_id && !$reason){
            $ret = [
                'message' => 'Fundraiser id and reason cannot be blank',
                'statusCode' => 200,
                'success' => false
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
                'success' => false
            ];
            return $ret;
        }
        $model = FundraiserScheme::find()->where(['id'=>$fundraiser_scheme_id])->one();
        if(!$model){
            $msg = "Invalid fundraiser.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => false
            ];
            return $ret;
        }
        $model->is_cancelled = 1;
        $model->cancellation_reason = $reason;
        $model->save(false);
        $ret = [
            'message' => 'Cancelled successfully',
            'statusCode' => 200,
            'success' => true
        ];
        return $ret;
    }
}


