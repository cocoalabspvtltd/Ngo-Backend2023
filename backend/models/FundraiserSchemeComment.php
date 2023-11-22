<?php

namespace backend\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "fundraiser_scheme_comment".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $comment
 * @property int|null $fundraiser_id
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class FundraiserSchemeComment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fundraiser_scheme_comment';
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
    public $name,$image_url,$from_date,$to_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'fundraiser_id', 'status'], 'integer'],
            [['comment'], 'string'],
            [['created_at', 'modified_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Added By',
            'comment' => 'Comment',
            'fundraiser_id' => 'Fundraiser Scheme',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
    
    public function getUser(){
        $modelUser = User::findOne($this->user_id);
        return ucfirst($modelUser->name);
    }
    public function getFundraiser(){
        $modelFundraiser = FundraiserScheme::findOne($this->id);
        return ucfirst($modelFundraiser->title);
    }
}
