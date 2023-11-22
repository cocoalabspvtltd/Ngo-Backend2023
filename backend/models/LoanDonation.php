<?php

namespace backend\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "loan_donation".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $loan_id
 * @property float|null $amount
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 * @property int|null $transaction_id
 */
class LoanDonation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan_donation';
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
    public $title,$purpose,$loan_amount,$image_url,$from_date,$to_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'loan_id', 'status', 'transaction_id','donated_by'], 'integer'],
            [['amount'], 'number'],
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
            'user_id' => 'User',
            'loan_id' => 'Loan',
            'amount' => 'Amount',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'transaction_id' => 'Transaction ID',
        ];
    }
    public function getLoan(){
        $model = Loan::findOne($this->loan_id);
        return ($model)?$model->title:'';
    }
    public function getUser(){
        $model = User::findOne($this->donated_by);
        return ($model)?$model->name:'';
    }
}
