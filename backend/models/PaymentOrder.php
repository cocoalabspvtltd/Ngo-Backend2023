<?php

namespace backend\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "payment_order".
 *
 * @property int $id
 * @property int|null $fundraiser_id
 * @property int|null $user_id
 * @property string|null $order_id
 * @property float|null $amount
 * @property float|null $converted_amount
 * @property string|null $currency
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class PaymentOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_order';
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
            [['fundraiser_id', 'user_id', 'status','loan_id'], 'integer'],
            [['amount', 'converted_amount'], 'number'],
            [['created_at', 'modified_at'], 'safe'],
            [['order_id'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fundraiser_id' => 'Fundraiser ID',
            'user_id' => 'User ID',
            'order_id' => 'Order ID',
            'amount' => 'Amount',
            'converted_amount' => 'Converted Amount',
            'currency' => 'Currency',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
