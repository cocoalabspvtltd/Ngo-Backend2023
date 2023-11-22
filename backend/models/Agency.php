<?php

namespace backend\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "agency".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $address
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 * @property string|null $email
 * @property string|null $phone
 */
class Agency extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agency';
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
    public $from_date,$to_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address'], 'string'],
            [['status'], 'integer'],
            [['created_at', 'modified_at','customer_id'], 'safe'],
            [['name', 'email'], 'string', 'min'=>3,'max' => 255],
            [['name','email'],'required'],
            //[['email'],'email'],
            [['phone'],'number'],
            [['phone'],'string','min'=>8,'max'=>15],
            [['name'],'unique']
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
            'address' => 'Address',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'email' => 'Email',
            'phone' => 'Phone',
        ];
    }
    
    public function getDonations()
    {
        return $this->hasMany(Donation::class, ['agency_id' => 'id']);
    }
}
