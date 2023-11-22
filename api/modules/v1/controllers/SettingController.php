<?php

namespace api\modules\v1\controllers;
use yii;
use yii\rest\ActiveController;
use backend\models\Setting;
use backend\models\Visitor;
use yii\data\ActiveDataProvider;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class SettingController extends ActiveController
{
    public $modelClass = 'backend\models\Setting';     
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
        $get = Yii::$app->request->get();
        $page = isset($get['page'])?$get['page']:1;
        $per_page = isset($get['per_page'])?$get['per_page']:20;
        $query = Setting::find()->where(['status'=>1]);
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
    public function actionAddVisitor(){
        header('Access-Control-Allow-Origin: *');
        $post = Yii::$app->request->post();
        $model = new Visitor;
        $model->count = 1;
        $model->save(false);
        $msg = 'Saved Successfully';
        $success = true;
        $statusCode = 200;
        $ret = [
            'message' => $msg,
            'success' => $success,
            'statusCode' => $statusCode
        ];
        return $ret;
    }
}


