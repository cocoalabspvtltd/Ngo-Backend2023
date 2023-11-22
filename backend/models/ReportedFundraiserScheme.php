<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "reported_fundraiser_scheme".
 *
 * @property int $id
 * @property int|null $fundraiser_scheme_id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $phone_number
 * @property string|null $email
 * @property string|null $description
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class ReportedFundraiserScheme extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reported_fundraiser_scheme';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fundraiser_scheme_id', 'user_id', 'status'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'modified_at'], 'safe'],
            [['name', 'email'], 'string', 'max' => 255],
            [['phone_number'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fundraiser_scheme_id' => 'Fundraiser Scheme ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'phone_number' => 'Phone Number',
            'email' => 'Email',
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
