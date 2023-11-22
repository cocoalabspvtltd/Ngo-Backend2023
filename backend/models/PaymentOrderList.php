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
class PaymentOrderList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_order_list';
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
            [[ 'status'], 'integer'],
            [['amount', 'converted_amount'], 'number'],
            [['created_at', 'modified_at'], 'safe'],
            [['order_id','name','email',], 'string', 'max' => 255],
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
            'name' => 'Name',
            'email' => 'Email',
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
