<?php

namespace backend\models;
use yii\helpers\Url;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "loan".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $purpose
 * @property float|null $amount
 * @property string|null $location
 * @property string|null $description
 * @property string|null $closing_date
 * @property string|null $image_url
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class Loan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan';
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
            [['purpose', 'location', 'description', 'image_url','virtual_account_type','virtual_account_id','virtual_account_number','virtual_account_ifsc','virtual_account_name'], 'string'],
            [['amount'], 'number'],
            [['closing_date', 'created_at', 'modified_at','created_by'], 'safe'],
            [['status','created_by','is_approved'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['title','purpose','amount','location','description','closing_date','created_by'],'required'],
            [['image_url'], 'file', 'maxFiles' => 1,'extensions' => 'png, jpg, jpeg'],
            [['amount'],'number']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'purpose' => 'Purpose',
            'amount' => 'Amount',
            'location' => 'Location',
            'description' => 'Description',
            'closing_date' => 'Closing Date',
            'image_url' => 'Image Url',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'is_approved' => 'Status'
        ];
    }
    public function getImage(){
        $imagePath = $this->image_url;
        $locationPath = Yii::$app->params['base_path_loan_images'];
        $path = $locationPath.$imagePath;
        return Url::to($path);
    }
    public function getUser(){
        $model = User::find()->where(['id'=>$this->created_by])->one();
        return $model->name;
    }
}
