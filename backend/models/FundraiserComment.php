<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "fundraiser_comment".
 *
 * @property int $id
 * @property int $sender_id
 * @property int $receiver_id
 * @property string $message
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class FundraiserComment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fundraiser_comment';
    }

    public $from_date,$to_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['message'], 'required'],
            [['sender_id', 'receiver_id', 'status','fundraiser_id'], 'integer'],
            [['message'], 'string'],
            [['created_at', 'modified_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender_id' => 'Sender ID',
            'receiver_id' => 'User',
            'message' => 'Message',
            'status' => 'Status',
            'created_at' => 'Created Date',
            'modified_at' => 'Modified At',
            'fundraiser_id' => 'Fundraiser Scheme'
        ];
    }
    public function getUser(){
        $model = User::find()->where(['id'=>$this->receiver_id])->one();
        return ($model)?ucfirst($model->name):'-';
    }
    public function getFundraiser(){
        $model = FundraiserScheme::find()->where(['id'=>$this->fundraiser_id])->one();
        return ($model)?ucfirst($model->title):'-';
    }
}
