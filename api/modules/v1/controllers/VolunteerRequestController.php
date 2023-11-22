<?php

namespace api\modules\v1\controllers;
use yii;
use yii\rest\ActiveController;
use backend\models\VolunteerRequests;
use yii\data\ActiveDataProvider;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class VolunteerRequestController extends ActiveController
{
    public $modelClass = 'backend\models\VolunteerRequests';   
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }  
    public function actionCreateVolunteer(){
        header('Access-Control-Allow-Origin: *');
        $post = Yii::$app->request->post();
        $name = isset($post['name'])?$post['name']:'';
        $address = isset($post['address'])?$post['address']:'';
        $phone_number = isset($post['phone_number'])?$post['phone_number']:'';
        $email = isset($post['email'])?$post['email']:'';
        $message = "";
        $success = false;
        if($name && $address && $phone_number && $email){
            $modelVolunteer = new VolunteerRequests;
            $modelVolunteer->name = $name;
            $modelVolunteer->address = $address;
            $modelVolunteer->email = $email;
            $modelVolunteer->phone_number = $phone_number;
            $modelVolunteer->save(false);
            $message = "Volunteer added successfully";
            $success = true;
        }else{
            $message = "Name, Address, Email, Phone Number cannot be blank";
        }
        $ret = [
            'statusCode' => 200,
            'success' => $success,
            'message' => $message
        ];
        return $ret;
    }
}