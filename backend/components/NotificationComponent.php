<?php
namespace backend\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
class NotificationComponent extends Component
{
    public function sendToOneUser($title,$value,$type=null,$typeVal=null){
        $contents = [
            'en' => $title
        ];
        $data = [];
        if($type){
            $data = [
                'type' => $type,
                'value' => $typeVal
            ];
        }
        $options = [
            'filters' => array(array("field" => "tag", "key" => "user_id", "relation" => "=", "value" => $value)),
            'data' => $data,
        ];
        $notification = Yii::$app->onesignal->notifications()->create($contents, $options);
        return $notification;
    }
    public function sendToAllUsers($title,$type=null,$typeVal=null){
        $contents = [
            'en' => $title
        ];
        $data = [];
        if($type){
            $data = [
                'type' => $type,
                'value' => $typeVal
            ];
        }
        $options = [
            'included_segments' => array('All'),
            'data' => $data,
        ];
        $notification = Yii::$app->onesignal->notifications()->create($contents, $options);
        return $notification;
    }
    
    public function actionSendNotification(){
        $title = "testing from common component";
        $notification = Yii::$app->notification->sendToOneUser($title,'57','fundraiser','10');
        echo "<pre>";print_r($notification);exit;
    }
}