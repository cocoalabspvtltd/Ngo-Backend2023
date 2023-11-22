<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "subscription".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $fundraiser_id
 * @property float|null $amount
 * @property string|null $plan_id
 * @property string|null $subscription_id
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class Subscription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscription';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'fundraiser_id', 'status','donation_id'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'modified_at'], 'safe'],
            [['plan_id', 'subscription_id'], 'string', 'max' => 255],
        ];
    }
    public $from_date,$to_date;
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'fundraiser_id' => 'Fundraiser Scheme',
            'amount' => 'Amount',
            'plan_id' => 'Plan ID',
            'subscription_id' => 'Subscription ID',
            'status' => 'Status',
            'created_at' => 'Subscription Date',
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
