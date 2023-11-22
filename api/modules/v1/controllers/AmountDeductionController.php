 <?php
 

error_reporting(E_ALL);
ini_set('display_errors', '1');

use backend\models\FundraiserScheme;
use backend\models\Transaction;
use yii;
use common\components\PaytmChecksums;
use phpDocumentor\Reflection\PseudoTypes\True_;
use yii\base\Response;
use yii\rest\ActiveController;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;

   
      
        $today=date('Y-m-d');
        $current_day= date("d", strtotime($date));  
        
        $query= FundraiserScheme::find()->all();
        // $query = FundraiserScheme::find()
        // ->leftJoin('transaction','transaction.fundraiser_id=fundraiser_scheme.id')
        // ->where(['fundraiser_id'=>$fundraiser_id])
        // ->select('fundraiser_scheme.fund_required,fundraiser_scheme.pricing_id,transaction.amount')->one();

        if($today==$query->closing_date)
        {
            echo "huj";
        }

        $amount= $query->amount;

        $pricing_id= $query->pricing_id;
//         if($pricing_id==1){
        
//             $pricing=0;
//         $aamount=$amount- $pricing;

//         }else if($pricing_id==2){
        
//             $pricing=5;
//         $aamount=$amount- $pricing;

//         }else if($pricing_id==3){

//             $pricing=8;
//             $aamount=$amount- $pricing;
//         }

