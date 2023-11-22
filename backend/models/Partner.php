<?php

namespace backend\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "partner".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $company
 * @property string|null $designation
 * @property string|null $email
 * @property string|null $phone_number
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class Partner extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner';
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
            [['status'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['name', 'company', 'designation'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 50],
            [['phone_number'], 'string', 'max' => 20],
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
            'name' => 'Name',
            'company' => 'Company',
            'designation' => 'Designation',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
