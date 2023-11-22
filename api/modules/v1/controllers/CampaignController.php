<?php

namespace api\modules\v1\controllers;
use yii;
use yii\rest\ActiveController;
use backend\models\Campaign;
use backend\models\CampaignDonation;
use yii\data\ActiveDataProvider;
use api\modules\v1\models\Account;
use backend\models\FundraiserScheme;
use backend\models\Certificate;
use backend\models\Point;
use backend\models\Donation;
use backend\models\Transaction;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class CampaignController extends ActiveController
{
    public $modelClass = 'backend\models\Campaign';    
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
        $baseUrl = Yii::$app->params['base_path_campaign_icons'];
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        $query = Campaign::find()->where(['status'=>1,'campaign_status'=>1]);
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
    
     public function actionDonate()
     {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $post = Yii::$app->request->post();
        $headers = getallheaders();
        $donor_type = isset($post['donor_type'])?$post['donor_type']:'';
        
         if($donor_type == 'Guest')
        {
            $campaign_id = isset($post['campaign_id'])?$post['campaign_id']:'';
            $amount = isset($post['amount'])?$post['amount']:'';
            $name = isset($post['name'])?$post['name']:'';
            $email = isset($post['email'])?$post['email']:'';
            $show_donor_information = isset($post['show_donor_information'])?$post['show_donor_information']:0;
            $transaction_id = isset($post['transaction_id'])?$post['transaction_id']:'';

            $certificate_name = isset($post['certificate_name'])?$post['certificate_name']:'';
            $certificate_address = isset($post['certificate_address'])?$post['certificate_address']:'';
            $certificate_phone = isset($post['certificate_phone'])?$post['certificate_phone']:'';
            $certificate_pan = isset($post['certificate_pan'])?$post['certificate_pan']:'';
        
            $modelFundraiser = FundraiserScheme::find()->where(['id'=>$campaign_id])->one();
            if(!$modelFundraiser)
            {
                $msg = "Invalid Campaign id.";
                $ret = [
                    'message' => $msg,
                    'statusCode' => 200,
                    'success' => $success
                ];
                return $ret;
            }
         
        if($campaign_id && $amount && $name && $email)
        {
            $modelCamDonation = new CampaignDonation;
            $modelCamDonation->campaign_id = $campaign_id;
            $modelCamDonation->name = $name;
            $modelCamDonation->email = $email;
            $modelCamDonation->amount = $amount;
            $modelCamDonation->status = 1;
            $modelCamDonation->show_donor_information = $show_donor_information;
            $modelCamDonation->transaction_id = $transaction_id;
            $modelCamDonation->save(false);
            
            if($modelCamDonation->save(false))
            {
            $modelDonation = new Donation;
            $modelDonation->fundraiser_id = $campaign_id;
            $modelDonation->name = $name;
            $modelDonation->email = $email;
            $modelDonation->amount = $amount;
            $modelDonation->show_donor_information = $show_donor_information;
            $modelDonation->transaction_id = $transaction_id;
            $modelDonation->save(false);
           
            
            if($modelDonation->save(false))
            {
                
            $modelTransaction = new Transaction;
            $modelTransaction->donor_name = $name;
            $modelTransaction->donor_email = $email;
            $modelTransaction->campaign_id = $campaign_id;
            $modelTransaction->payment_id = $transaction_id;
            $modelTransaction->amount = $amount;
            $modelTransaction->save(false);
            
            }
            
            }
            if($certificate_name && $certificate_address && $certificate_phone && $certificate_pan){
                $modelCertificate = new Certificate;
                $modelCertificate->name = $certificate_name;
                $modelCertificate->address = $certificate_address;
                $modelCertificate->phone_number = $certificate_phone;
                $modelCertificate->pan_number = $certificate_pan;
                $modelCertificate->fundraiser_id = $campaign_id;
                $modelCertificate->save(false);
            }

            $totalAmountCollected = CampaignDonation::find()->where(['status'=>1,'campaign_id'=>$campaign_id])->sum('amount');
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
            $msg = 'Campaign, Amount, Name, Email cannot be blank';
        }
           
        $ret = [
            'statusCode' => $statusCode,
            'success' => $success,
            'message' => $msg
        ];
        return $ret;
    }
    else
    {
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
        $amount = isset($post['amount'])?$post['amount']:'';
        $name = isset($post['name'])?$post['name']:'';
        $email = isset($post['email'])?$post['email']:'';
        $show_donor_information = isset($post['show_donor_information'])?$post['show_donor_information']:0;
        $transaction_id = isset($post['transaction_id'])?$post['transaction_id']:'';

        $certificate_name = isset($post['certificate_name'])?$post['certificate_name']:'';
        $certificate_address = isset($post['certificate_address'])?$post['certificate_address']:'';
        $certificate_phone = isset($post['certificate_phone'])?$post['certificate_phone']:'';
        $certificate_pan = isset($post['certificate_pan'])?$post['certificate_pan']:'';
        
        $modelFundraiser = FundraiserScheme::find()->where(['id'=>$campaign_id])->one();
        if(!$modelFundraiser){
            $msg = "Invalid Campaign id.";
            $ret = [
                'message' => $msg,
                'statusCode' => 200,
                'success' => $success
            ];
            return $ret;
        }
         
       
        if($campaign_id && $amount && $name && $email){
            $modelCamDonation = new CampaignDonation;
            $modelCamDonation->campaign_id = $campaign_id;
            $modelCamDonation->user_id = $userId;
            $modelCamDonation->name = $name;
            $modelCamDonation->email = $email;
            $modelCamDonation->amount = $amount;
            $modelCamDonation->status = 1;
            $modelCamDonation->show_donor_information = $show_donor_information;
            $modelCamDonation->transaction_id = $transaction_id;
            $modelCamDonation->donated_by = $userId;
            $modelCamDonation->save(false);
            
            if($modelCamDonation->save(false))
            {
               $modelDonation = new Donation;
               $modelDonation->fundraiser_id = $campaign_id;
               $modelDonation->user_id = $userId;
               $modelDonation->name = $name;
               $modelDonation->email = $email;
               $modelDonation->amount = $amount;
               $modelDonation->show_donor_information = $show_donor_information;
               $modelDonation->transaction_id = $transaction_id;
               $modelDonation->donated_by = $userId;
               $modelDonation->save(false);
            }
             
            if($modelDonation->save(false))
            {
                
            $modelTransaction = new Transaction;
            $modelTransaction->donor_name = $name;
            $modelTransaction->donor_email = $email;
            $modelTransaction->campaign_id = $campaign_id;
            $modelTransaction->payment_id = $transaction_id;
            $modelTransaction->amount = $amount;
            $modelTransaction->save(false);
            
            }
            if($certificate_name && $certificate_address && $certificate_phone && $certificate_pan){
                $modelCertificate = new Certificate;
                $modelCertificate->name = $certificate_name;
                $modelCertificate->address = $certificate_address;
                $modelCertificate->phone_number = $certificate_phone;
                $modelCertificate->pan_number = $certificate_pan;
                $modelCertificate->user_id = $userId;
                $modelCertificate->fundraiser_id = $campaign_id;
                $modelCertificate->save(false);
            }

            $modelPoint = Point::find()->where(['status'=>1,'title'=>'donate'])->one();
            if($modelPoint && $modelPoint->point){
                $point = $modelPoint->point;
                $user_details->points = $user_details->points + $point;
                $user_details->save(false);
            }

            $totalAmountCollected = CampaignDonation::find()->where(['status'=>1,'campaign_id'=>$campaign_id])->sum('amount');
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
            $msg = 'Campaign, Amount, Name, Email cannot be blank';
        }
           
        $ret = [
            'statusCode' => $statusCode,
            'success' => $success,
            'message' => $msg
        ];
        return $ret;
     }
    
    }
}


