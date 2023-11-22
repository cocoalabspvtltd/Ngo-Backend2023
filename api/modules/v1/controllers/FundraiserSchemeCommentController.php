<?php

namespace api\modules\v1\controllers;
use yii;
use yii\rest\ActiveController;
use backend\models\FundraiserScheme;
use backend\models\FundraiserSchemeComment;
use yii\data\ActiveDataProvider;
use  api\modules\v1\models\Account;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class FundraiserSchemeCommentController extends ActiveController
{
    public $modelClass = 'backend\models\FundraiserSchemeComment';      
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }  
    public function actionAddComment(){
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
        $fundraiser_id = isset($post['fundraiser_id'])?$post['fundraiser_id']:'';
        $comment = isset($post['comment'])?$post['comment']:'';
        if($fundraiser_id && $comment){
            $modelFundraiserSchemeComment = new FundraiserSchemeComment;
            $modelFundraiserSchemeComment->fundraiser_id = $fundraiser_id;
            $modelFundraiserSchemeComment->user_id = $userId;
            $modelFundraiserSchemeComment->comment = $comment;
            $modelFundraiserSchemeComment->save(false);
            $success = true;
            $msg = "Comment received successfully";
        }else{
            $msg = 'Fundraiser, Comment cannot be blank';
        }
        $ret = [
            'statusCode' => $statusCode,
            'success' => $success,
            'message' => $msg
        ];
        return $ret;
    }
    public function actionList(){
        header('Access-Control-Allow-Origin: *');
        $ret = [];
        $statusCode = 200;
        $success = false;
        $msg = '';
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        $fundraiser_id = isset($get['fundraiser_id'])?$get['fundraiser_id']:'';
        if(!$fundraiser_id){
            $msg = 'Fundraiser, Comment cannot be blank';
            $ret = [
                'statusCode' => 200,
                'page' => (int) $page,
                'perPage' => (int) $per_page,
                'message' => $msg,
                'success' => $success
            ];
            return $ret;
        }
        $query = FundraiserSchemeComment::find()
        ->leftJoin('fundraiser_scheme','fundraiser_scheme.id=fundraiser_scheme_comment.fundraiser_id')
        ->leftJoin('user','user.id=fundraiser_scheme_comment.user_id')
        ->where(['fundraiser_scheme_comment.status'=>1])
        ->andWhere(['fundraiser_scheme_comment.fundraiser_id'=>$fundraiser_id])
        ->select('fundraiser_scheme_comment.*,user.name,user.image_url');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [$page, $per_page]
            ]
        ]);
        $list = [];
        if($dataProvider->getModels()){
            foreach($dataProvider->getModels() as $fundraiserModel){
                $list[] = [
                    'fundraiser_id' => $fundraiserModel->fundraiser_id,
                    'name' => $fundraiserModel->name,
                    'image_url' => $fundraiserModel->image_url,
                    'comment' => $fundraiserModel->comment,
                    'created_date' => date('d M Y h:i a',strtotime($fundraiserModel->created_at)),
                    'created_at' => $fundraiserModel->created_at,
                ];
            }
        }
        $hasNextPage = false;
        if(($page*$per_page) < ($query->count())){
            $hasNextPage = true;
        }
        $baseUrl = Yii::$app->params['base_path_profile_images'];
        $ret = [
            'statusCode' => 200,
            'baseUrl' => $baseUrl,
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
    public function actionMyComments(){
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
        $query = FundraiserSchemeComment::find()
        ->leftJoin('fundraiser_scheme','fundraiser_scheme.id=fundraiser_scheme_comment.fundraiser_id')
        ->leftJoin('user','user.id=fundraiser_scheme_comment.user_id')
        ->where(['fundraiser_scheme_comment.status'=>1])
        ->andWhere(['fundraiser_scheme_comment.user_id'=>$user_details->id])
        ->select('fundraiser_scheme_comment.*,fundraiser_scheme.title as name,fundraiser_scheme.image_url as image_url');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [$page, $per_page]
            ]
        ]);
        $list = [];
        if($dataProvider->getModels()){
            foreach($dataProvider->getModels() as $fundraiserModel){
                $list[] = [
                    'fundraiser_id' => $fundraiserModel->fundraiser_id,
                    'name' => $fundraiserModel->name,
                    'image_url' => $fundraiserModel->image_url,
                    'comment' => $fundraiserModel->comment,
                    'created_date' => date('d M Y h:i a',strtotime($fundraiserModel->created_at)),
                    'created_at' => $fundraiserModel->created_at,
                ];
            }
        }
        $hasNextPage = false;
        if(($page*$per_page) < ($query->count())){
            $hasNextPage = true;
        }
        $baseUrl = Yii::$app->params['base_path_profile_images'];
        $ret = [
            'statusCode' => 200,
            'baseUrl' => $baseUrl,
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
}