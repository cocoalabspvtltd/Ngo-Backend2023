<?php

namespace api\modules\v1\controllers;
use yii;
use yii\rest\ActiveController;
use backend\models\Media;
use yii\data\ActiveDataProvider;
use backend\models\FundraiserScheme;
use backend\models\AgencyDonation;
use backend\models\Transaction;
use backend\models\DeductedAmount;
use backend\models\AgencyLandingPage;
use backend\models\Campaign;
use backend\models\FundraiserSchemeDocuments;
use backend\models\Donation;
use api\modules\v1\models\Account;
use yii\web\UploadedFile;
use backend\models\User;


/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class MediaController extends ActiveController
{
    public $modelClass = 'backend\models\Media';     
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
        $baseUrl = Yii::$app->params['base_path_media_images'];
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        $query = Media::find()->where(['status'=>1]);
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
    
    
    public function actionAgencyDonate(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $post = Yii::$app->request->post();
        $headers = getallheaders();
        $agency_id = isset($post['agency_id'])?$post['agency_id']:'';
        $fundraiser_id = isset($post['fundraiser_scheme_id'])?$post['fundraiser_scheme_id']:'';
        $amount = isset($post['amount'])?$post['amount']:'';
        $name = isset($post['name'])?$post['name']:'';
        $email = isset($post['email'])?$post['email']:'';
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
         
        if($fundraiser_id && $amount && $name && $email)
        {
            $modelAgencyDonation = new AgencyDonation;
            $modelAgencyDonation->fundraiser_id = $fundraiser_id;
            $modelAgencyDonation->agency_id = $agency_id;
            $modelAgencyDonation->name = $name;
            $modelAgencyDonation->email = $email;
            $modelAgencyDonation->amount = $amount;
            $modelAgencyDonation->transaction_id = $transaction_id;
            $modelAgencyDonation->save(false);
            
            $modelDonation = new Donation;
            $modelDonation->name = $name;
            $modelDonation->agency_id = $agency_id;
            $modelDonation->fundraiser_id = $fundraiser_id;
            $modelDonation->email = $email;
            $modelDonation->amount = $amount;
            $modelDonation->show_donor_information =1;
            $modelDonation->transaction_id = $transaction_id;
            $modelDonation->save(false);
            
            if($modelAgencyDonation->save())
            {
                
            $modelTransaction = new Transaction;
            $modelTransaction->donor_name = $name;
            $modelTransaction->donor_email = $email;
            $modelTransaction->fundraiser_id = $fundraiser_id;
            $modelTransaction->txn_id = $transaction_id;
            $modelTransaction->amount = $amount;
            $modelTransaction->save(false);
            
             $modeldeduction = new DeductedAmount;
             $modeldeduction->fundraiser_id =$fundraiser_id;
             $modeldeduction->donor_name =$name;
             $modeldeduction->donor_email= $email;
             $modeldeduction->amount= $amount;
             $modeldeduction->save(false);
             
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
            if($certificate_name && $certificate_address && $certificate_phone && $certificate_pan){
                $modelCertificate = new Certificate;
                $modelCertificate->name = $certificate_name;
                $modelCertificate->address = $certificate_address;
                $modelCertificate->phone_number = $certificate_phone;
                $modelCertificate->pan_number = $certificate_pan;
                $modelCertificate->user_id = $userId;
                $modelCertificate->fundraiser_id = $fundraiser_id;
                $modelCertificate->save(false);
            }
            $AgencyAmountCollected = AgencyDonation::find()->where(['fundraiser_id'=>$fundraiser_id])->sum('amount');
            $AmountCollected = Donation::find()->where(['fundraiser_id'=>$fundraiser_id])->sum('amount');
            $totalAmountCollected = $AgencyAmountCollected + $AmountCollected;
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
    
}


