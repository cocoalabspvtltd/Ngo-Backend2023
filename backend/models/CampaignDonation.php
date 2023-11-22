<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "campaign_donation".
 *
 * @property int $id
 * @property int $campaign_id
 * @property int $user_id
 * @property string $name
 * @property string $email
 * @property string $amount
 * @property int $show_donor_information
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 * @property string $transaction_id
 * @property int $donated_by
 */
class CampaignDonation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'campaign_donation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['campaign_id', 'user_id', 'name', 'email', 'amount', 'show_donor_information', 'status', 'created_at', 'modified_at', 'transaction_id', 'donated_by'], 'required'],
            [['campaign_id', 'user_id', 'show_donor_information', 'status', 'donated_by'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['name', 'email', 'transaction_id'], 'string', 'max' => 255],
            [['amount'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'campaign_id' => 'Campaign ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'email' => 'Email',
            'amount' => 'Amount',
            'show_donor_information' => 'Show Donor Information',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'transaction_id' => 'Transaction ID',
            'donated_by' => 'Donated By',
        ];
    }

    /**
     * {@inheritdoc}
     * @return CampaignDonationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CampaignDonationQuery(get_called_class());
    }
}
