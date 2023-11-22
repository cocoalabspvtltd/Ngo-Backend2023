<?php

namespace api\modules\v1\controllers;
use yii;
use yii\rest\ActiveController;
use backend\models\ContactUs;
use yii\data\ActiveDataProvider;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class ContactUsController extends ActiveController
{
    public $modelClass = 'backend\models\ContactUs';    
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }    
    public function  actionAddContact(){
        header('Access-Control-Allow-Origin: *');
        $post = Yii::$app->request->post();
        $msg = "";
        $statusCode = 200;
        $success = false;
        $ret = [];
        $name = isset($post['name'])?$post['name']:'';
        $email = isset($post['email'])?$post['email']:'';
        $message = isset($post['message'])?$post['message']:'';
        if($name && $email && $message){
            $modelContact = new ContactUs;
            $modelContact->name = $name;
            $modelContact->email = $email;
            $modelContact->message = $message;
            $modelContact->save(false);
            $msg = "Our representative will contact you soon";
            $success = true;
        }else{
            $msg = "Name, Email and Message cannot be blank";
        }
        $ret = [
            'statusCode' => $statusCode,
            'success' => $success,
            'message' => $msg
        ];
        return $ret;
    }
}


