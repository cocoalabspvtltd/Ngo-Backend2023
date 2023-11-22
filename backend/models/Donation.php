<?php

namespace backend\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "donation".
 *
 * @property int $id
 * @property int|null $fundraiser_id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $email
 * @property float|null $amount
 * @property int|null $show_donor_information
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 * @property int|null $transaction_id
 */
class Donation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'donation';
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
    public $image_url,$from_date,$to_date,$title;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fundraiser_id', 'user_id', 'show_donor_information', 'status','donated_by'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'modified_at','transaction_id'], 'safe'],
            [['name', 'email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fundraiser_id' => 'Fundraiser Scheme',
            'user_id' => 'User',
            'name' => 'Name',
            'email' => 'Email',
            'amount' => 'Amount',
            'show_donor_information' => 'Anonymous Or Not',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'transaction_id' => 'Transaction ID',
        ];
    }

    public function getFundraiser(){
        $model = FundraiserScheme::findOne($this->fundraiser_id);
        return ($model)?$model->title:'';
    }
    
    public function getCampaign(){
        $models = Campaign::find()->all();
    $titles = [];

    foreach ($models as $model) {
        $titles[] = $model->title;
    }

    return $titles;
    }
    
    public function getUser(){
        $model = User::findOne($this->donated_by);
        return ($model)?$model->name:'';
    }
    
    public function getFundraiserName()
    {
          $model = FundraiserScheme::findOne($this->fundraiser_id);
          
          if($model->fundaiser_id == null)
          {
              return "Crowd Workd India Foundation";
          }
          else{
             
          }
    }
    
    public function getCertificate()
    {
      return $this->hasOne(Certificate::class, ['donation_id' => 'id']);
    }
    
    public function getAgency()
    {
        return $this->hasOne(Agency::class, ['id' => 'agency_id']);
    }
    
     public function getCampaigns()
    {
        return $this->hasOne(Campaign::class, ['id' => 'campaign_id']);
    }
}
