<?php

namespace backend\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transfer_request".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $fundraiser_id
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class TransferRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transfer_request';
    }
    public $from_date,$to_date;
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
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'fundraiser_id', 'status'], 'integer'],
            [['created_at', 'modified_at','transferred_amount'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'fundraiser_id' => 'Fundraiser',
            'status' => 'Status',
            'created_at' => 'Requested Date',
            'modified_at' => 'Modified At',
        ];
    }
    public function getUser(){
        $model = User::find()->where(['id'=>$this->user_id])->one();
        return ($model)?$model->name:'';
    }
    public function getFundraiser(){
        $model = FundraiserScheme::find()->where(['id'=>$this->fundraiser_id])->one();
        return ($model)?$model->title:'';
    }
}
