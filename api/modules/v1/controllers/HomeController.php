<?php

namespace api\modules\v1\controllers;
use yii;
use yii\rest\ActiveController;
use backend\models\FundraiserScheme;
use backend\models\Campaign;
use yii\data\ActiveDataProvider;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class HomeController extends ActiveController
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
        $campaignBaseUrl = Yii::$app->params['base_path_campaign_icons'];
        $fundraiserBaseUrl = Yii::$app->params['base_path_fundraiser_images'];
        $get = Yii::$app->request->get();
        $date = date('Y-m-d');
        // $query = FundraiserScheme::find()
        // ->where(['status'=>1])
        // ->limit(8)
        // ->andWhere(['>=','closing_date',$date])
        // ->andWhere(['is_amount_collected'=>0]);
        $query = FundraiserScheme::find()
        ->leftJoin('user','user.id=fundraiser_scheme.created_by')
        ->where(['fundraiser_scheme.status'=>1,'is_approved'=>1])
        ->limit(8)
        ->andWhere(['>=','closing_date',$date])
        ->andWhere(['user.role'=>'campaigner'])
        ->andWhere(['is_amount_collected'=>0]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
        $recommendedQuery = FundraiserScheme::find()
        ->where(['status'=>1])
        ->limit(8)
        ->andWhere(['>=','closing_date',$date])
        ->andWhere(['is_amount_collected'=>0]);
        $recommemdedDataProvider = new ActiveDataProvider([
            'query' => $recommendedQuery,
            'pagination' => false
        ]);
        $recommendedQuery->orderBy("closing_date ASC");
        $campagnQuery = Campaign::find()->where(['status'=>1,'campaign_status'=>1]);
        $campaignDataProvider = new ActiveDataProvider([
            'query' => $campagnQuery,
            'pagination' => false
        ]);
        $webBaseUrl = 'https://www.cocoalabs.in/ngo-web/ngo/fund-raiser-detail/';
        $ret = [
            'statusCode' => 200,
            'success' => true,
            'fundraiserBaseUrl' => $fundraiserBaseUrl,
            'webBaseUrl' => $webBaseUrl,
            'campaignBaseUrl' => $campaignBaseUrl,
            'campaignList' => $campaignDataProvider,
            'fundraiserList' => $dataProvider,
            'recommendedList' => $recommemdedDataProvider,
            'message' => 'Listed Successfully'
        ];
        return $ret;
    }
    public function actionCorsIssue(){
        header('Access-Control-Allow-Origin: *');
        $get = Yii::$app->request->get();
        $items = [
            'apple' => 'Apple',
            'mango' => 'Mango',
            'grapes' => 'Grapes'
        ];
        $ret = [
            'success' => true,
            'message' => 'Issue Solved',
            'items' => $items
        ];
        return $ret;   
    }
    public function actionHeaderCorsIssue(){
        $get = Yii::$app->request->get();
        $headers = getallheaders();
        $api_token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : '');
        $ret = [
            'api_token' => $api_token,
            'success' => true,
            'message' => 'Solved'
        ];
        return $ret;
    }
}
 

