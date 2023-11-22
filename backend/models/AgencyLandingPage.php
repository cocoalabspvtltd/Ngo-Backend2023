<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "agency_landing_page".
 *
 * @property int $id
 * @property int|null $agency_id
 * @property int|null $fundraiser_scheme_id
 * @property string|null $landing_page_url
 * @property string|null $virtual_account_id
 * @property string|null $virtual_account_number
 * @property string|null $virtual_account_name
 * @property string|null $virtual_account_type
 * @property string|null $virtual_account_ifsc
 * @property float $total_amount_collected
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class AgencyLandingPage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agency_landing_page';
    }
    public $from_date,$to_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agency_id', 'fundraiser_scheme_id', 'status'], 'integer'],
            [['total_amount_collected'], 'number'],
            [['created_at', 'modified_at'], 'safe'],
            [['landing_page_url', 'virtual_account_id', 'virtual_account_number', 'virtual_account_name', 'virtual_account_type', 'virtual_account_ifsc'], 'string', 'max' => 255],
            [['agency_id','fundraiser_scheme_id'],'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_id' => 'Agency',
            'fundraiser_scheme_id' => 'Fundraiser Scheme',
            'landing_page_url' => 'Landing Page Url',
            'virtual_account_id' => 'Virtual Account ID',
            'virtual_account_number' => 'Virtual Account Number',
            'virtual_account_name' => 'Virtual Account Name',
            'virtual_account_type' => 'Virtual Account Type',
            'virtual_account_ifsc' => 'Virtual Account Ifsc',
            'total_amount_collected' => 'Total Amount Collected',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
    public function getAgency(){
        $model = Agency::find()->where(['id'=>$this->agency_id])->one();
        return ($model)?$model->name:'';
    }
    public function getFundraiser(){
        $model = FundraiserScheme::find()->where(['id'=>$this->fundraiser_scheme_id])->one();
        return ($model)?$model->title:'';
    }
}
