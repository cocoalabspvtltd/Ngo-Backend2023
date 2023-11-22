<?php

namespace backend\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transaction".
 *
 * @property int $id
 * @property string|null $reference_no
 * @property string|null $payment_type
 * @property string|null $payment_status
 * @property string|null $payment_method
 * @property float|null $amount
 * @property string|null $tag
 * @property string|null $channel
 * @property int|null $status
 * @property string|null $payment_initiated_at
 * @property string|null $payment_failed_at
 * @property string|null $payment_completed_at
 * @property string|null $created_at
 * @property string|null $modified_at
 */
class Transaction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction';
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
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount','deducted_amount'], 'number'],
            [['status','fundraiser_id','campaign_id'], 'integer'],
            [['payment_initiated_at', 'payment_failed_at', 'payment_completed_at', 'created_at', 'modified_at'], 'safe'],
            [['txn_id','tag', 'channel','donor_name','donor_email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
      return [
            'id' => 'ID',
            'txn_id' => 'TXN ID',
            'donor_name' => 'Donor Name',
            'donor_email' => 'Donor Email',
            'fundraiser_id' => 'Fundraiser ID',
            'campaign_id' => 'Campaign ID',
            'amount' => 'Amount',
            'deducted_amount' => 'Deducted Amount',
            'tag' => 'Tag',
            'channel' => 'Channel',
            'status' => 'Status',
            'payment_initiated_at' => 'Payment Initiated At',
            'payment_failed_at' => 'Payment Failed At',
            'payment_completed_at' => 'Payment Completed At',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
