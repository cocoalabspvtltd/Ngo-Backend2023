<?php

namespace backend\models;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string|null $username
 * @property string|null $password_hash
 * @property string|null $auth_key
 * @property string|null $role
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'modified_at',
                'value' => new Expression('NOW()'),
            ]
        ];
    }
    public $new_password,$confirm_password,$from_date,$to_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['created_at', 'modified_at','name','email','phone_number','country_code','customer_id','virtual_account_id'], 'safe'],
            [['username', 'password_hash', 'auth_key', 'role'], 'string', 'max' => 255],
            [['confirm_password','new_password'],'string','min'=>6],
            [['new_password'],'is_pass_required'],
            ['confirm_password', 'compare', 'compareAttribute'=>'new_password', 'message'=>"Passwords don't match" ],
            [['points'],'number'],
            [['image_url'],'file', 'maxFiles' => 1,'extensions' => 'png, jpg, jpeg'],
        ];
    } 

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password_hash' => 'Password Hash',
            'auth_key' => 'Auth Key',
            'role' => 'Role',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'image_url' => 'Image'
        ];
    }
    public function is_pass_required($attribute){
        $password = $this->new_password;
        $newPassword = $this->confirm_password;
        if($password && $newPassword == ''){
            $this->addError('confirm_password','Confirm password cannot be blank.');
            return false;
        }
    }
    public function uploadAndSave($images,$params=null)
    {
       $retId = false;
       if(!$images)
  	     $images = UploadedFile::getInstances($this,'uploaded_files');
       if(!is_array($images))
       {
         $images = [$images];
         $retId = true;
       }
       $ret= [];
       $uploads_path = Yii::$app->params['uploads_path'];
       if($params){
         $uploads_path = $params;
       }
       foreach($images as $image) {
            $newImage = User::renameImage($image);
            $image_path = $uploads_path.$newImage;
            $image_full_path = Yii::getAlias($image_path);
            $image->saveAs($image_full_path);
            $ret = $newImage;
       }
       return $ret;
    }
    public static function renameImage($image)
    {
        $name_tmp = isset($image->name)?$image->name:$image['name'];
        $name_tmp = explode('.',$name_tmp );
        $ext = $name_tmp[sizeof($name_tmp)-1];
        array_pop($name_tmp);
        $unique_num  = sha1(time());
        $name_tmp[] = $unique_num;
        $name_tmp = implode('-',$name_tmp).'.'.$ext;
        $image = $name_tmp;
        return $image;
    }
    public function getImage(){
        $imagePath = $this->image_url;
        $locationPath = Yii::$app->params['base_path_profile_images'];
        $path = $locationPath.$imagePath;
        return Url::to($path);
    }
}
