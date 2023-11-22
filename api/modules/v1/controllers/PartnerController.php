<?php

namespace api\modules\v1\controllers;
use yii;
use yii\rest\ActiveController;
use backend\models\Partner;
use yii\data\ActiveDataProvider;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class PartnerController extends ActiveController
{
    public $modelClass = 'backend\models\Partner';    
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }    
    public function  actionAddPartner(){
        header('Access-Control-Allow-Origin: *');
        $post = Yii::$app->request->post();
        $msg = "";
        $statusCode = 200;
        $success = false;
        $ret = [];
        $name = isset($post['name'])?$post['name']:'';
        $email = isset($post['email'])?$post['email']:'';
        $phone_number = isset($post['phone_number'])?$post['phone_number']:'';
        $company = isset($post['company'])?$post['company']:'';
        $designation = isset($post['designation'])?$post['designation']:'';
        if($name && $email && $phone_number && $company && $designation){
            $modelPartner = new Partner;
            $modelPartner->name = $name;
            $modelPartner->email = $email;
            $modelPartner->company = $company;
            $modelPartner->designation = $designation;
            $modelPartner->phone_number = $phone_number;
            $modelPartner->save(false);
            $msg = "Our representative will contact you soon";
            $success = true;
        }else{
            $msg = "Name, Email, Company, Designation and Phone number cannot be blank";
        }
        $ret = [
            'statusCode' => $statusCode,
            'success' => $success,
            'message' => $msg
        ];
        return $ret;
    }
}


