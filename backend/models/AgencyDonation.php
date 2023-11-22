<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "agency_donation".
 *
 * @property int $id
 * @property int $agency_id
 * @property int $fundraiser_id
 * @property string $name
 * @property string $email
 * @property int $amount
 * @property string $transaction_id
 * @property string $created_at
 * @property string $modified_at
 */
class AgencyDonation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agency_donation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agency_id', 'fundraiser_id', 'name', 'email', 'amount', 'transaction_id'], 'required'],
            [['agency_id', 'fundraiser_id', 'amount'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['name', 'email', 'transaction_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_id' => 'Agency ID',
            'fundraiser_id' => 'Fundraiser ID',
            'name' => 'Name',
            'email' => 'Email',
            'amount' => 'Amount',
            'transaction_id' => 'Transaction ID',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }

    /**
     * {@inheritdoc}
     * @return AgencyDonationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AgencyDonationQuery(get_called_class());
    }
}
