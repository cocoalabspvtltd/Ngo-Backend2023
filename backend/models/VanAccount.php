<?php

namespace backend\models;

use yii\base\Model;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

class VanAccount extends \yii\db\ActiveRecord
{
    // public $user_id;
    // public $van;
    // public $identificationNo;
    // public $active;
    // public $ifscCode;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'van_account';
    }
    // public function behaviors()
    // {
    //     return [
    //         [
    //             'class' => TimestampBehavior::className(),
    //             'createdAtAttribute' => 'created_at',
    //             'updatedAtAttribute' => 'modified_at',
    //             'value' => new Expression('NOW()'),
    //         ]
    //     ];
    // }
    /**
     * {@inheritdoc}
     */
    // public function attributeLabels()
    // {
    //     return [
    //         'user_id' => 'User ID',
    //         'van' => 'Virtual account number',
    //         'identificationNo' => 'Identification number',
    //         'active' => 'Account status',
    //         'ifscCode' => 'IFSC code'
    //     ];
    // }
    // /**
    //  * {@inheritdoc}
    //  */
    // public function rules()
    // {
    //     return [
    //         ['user_id', 'required'],
    //         ['van', 'required'],
    //         ['identificationNo', 'required'],
    //         ['active', 'required'],
    //         ['ifscCode', 'required']
    //     ];
    // }
}
