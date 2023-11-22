<?php

namespace backend\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "certificate".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $address
 * @property string|null $phone_number
 * @property string|null $pan_number
 * @property int $is_form_generated
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 * @property int|null $user_id
 * @property int|null $fundraiser_id
 */
class Certificate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'certificate';
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
            [['is_form_generated', 'status', 'user_id', 'fundraiser_id'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['name', 'pan_number'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'address' => 'Address',
            'phone_number' => 'Phone Number',
            'pan_number' => 'Pan Number',
            'is_form_generated' => 'Is Form Generated',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'user_id' => 'User',
            'fundraiser_id' => 'Fundraiser',
        ];
    }

    public function getFundraiser(){
        $model = FundraiserScheme::findOne($this->fundraiser_id);
        return ($model)?$model->title:'';
    }
    public function getUser(){
        $model = User::findOne($this->user_id);
        return ($model)?$model->name:'';
    }
}
