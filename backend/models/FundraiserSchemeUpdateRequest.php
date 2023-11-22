<?php

namespace backend\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "fundraiser_scheme_update_request".
 *
 * @property int $id
 * @property int|null $fundraiser_id
 * @property int|null $campaign_id
 * @property string|null $image_url
 * @property string|null $title
 * @property float|null $fund_required
 * @property string|null $closing_date
 * @property string|null $story
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone_number
 * @property float|null $country_code
 * @property int|null $relation_master_id
 * @property string|null $patient_name
 * @property string|null $health_issue
 * @property string|null $hospital
 * @property string|null $city
 * @property string|null $beneficiary_account_name
 * @property string|null $beneficiary_account_number
 * @property string|null $beneficiary_bank
 * @property string|null $beneficiary_ifsc
 * @property string|null $beneficiary_image
 * @property int|null $created_by
 * @property int|null $pricing_id
 */
class FundraiserSchemeUpdateRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fundraiser_scheme_update_request';
    }
    public $from_date,$to_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fundraiser_id', 'campaign_id', 'status', 'relation_master_id', 'created_by', 'pricing_id','is_approved'], 'integer'],
            [['title', 'story', 'health_issue', 'hospital', 'beneficiary_account_name', 'beneficiary_account_number', 'beneficiary_bank', 'beneficiary_ifsc', 'beneficiary_image'], 'string'],
            [['fund_required', 'country_code'], 'number'],
            [['closing_date', 'created_at', 'modified_at'], 'safe'],
            [['image_url', 'name', 'email', 'phone_number', 'patient_name', 'city'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fundraiser_id' => 'Fundraiser Scheme',
            'campaign_id' => 'Purpose Of Raising Fund',
            'image_url' => 'Image Url',
            'title' => 'Fundraiser Scheme',
            'fund_required' => 'Fund Required',
            'closing_date' => 'Closing Date',
            'story' => 'Story',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'name' => 'Name',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'country_code' => 'Country Code',
            'relation_master_id' => 'Relation Master ID',
            'patient_name' => 'Patient Name',
            'health_issue' => 'Health Issue',
            'hospital' => 'Hospital',
            'city' => 'City',
            'beneficiary_account_name' => 'Beneficiary Account Name',
            'beneficiary_account_number' => 'Beneficiary Account Number',
            'beneficiary_bank' => 'Beneficiary Bank',
            'beneficiary_ifsc' => 'Beneficiary Ifsc',
            'beneficiary_image' => 'Beneficiary Image',
            'created_by' => 'Update Requested By',
            'pricing_id' => 'Pricing ID',
            'is_approved' => 'Status'
        ];
    }

    
    public function getImage(){
        $imagePath = $this->image_url;
        $locationPath = Yii::$app->params['base_path_fundraiser_images'];
        $path = $locationPath.$imagePath;
        return Url::to($path);
    }
    public function getBeneficiaryImage(){
        $imagePath = $this->beneficiary_image;
        if($imagePath){
            $locationPath = Yii::$app->params['base_path_fundraiser_images'];
            $path = $locationPath.$imagePath;
            return Url::to($path);
        }
    }
    public function getDocuments(){
        $modelDocuments = FundraiserSchemeDocuments::find()->where(['status'=>1,'fundraiser_scheme_id'=>$this->id])->all();
        $list = [];
        if($modelDocuments){
            foreach($modelDocuments as $documents){
                $imagePath = $documents->doc_url;
                if($imagePath){
                    $locationPath = Yii::$app->params['base_path_fundraiser_documents'];
                    $path = $locationPath.$imagePath;
                    $list[] = [
                        'url' => Url::to($path),
                        'id' => $documents->id
                    ];
                }
            }
        }
        return $list;
    }
    public function getCampaign(){
        $modelCampaign = Campaign::findOne($this->campaign_id);
        return ucfirst($modelCampaign->title);
    }
    public  function getRelationMaster(){
        $modelRelationMaster = RelationMaster::findOne($this->relation_master_id);
        return ($modelRelationMaster)?ucfirst($modelRelationMaster->title):'';
    }
    public  function getPricing(){
        $modelPricingMaster = PricingMaster::findOne($this->pricing_id);
        return ($modelPricingMaster)?ucfirst($modelPricingMaster->title):'';
    }
    public  function getUser(){
        $modelUser = User::findOne($this->created_by);
        return ($modelUser)?ucfirst($modelUser->name):'';
    }
    public  function getFundraiser(){
        if($this->title){
            return $this->title;
        }else{
            $model = FundraiserScheme::findOne($this->fundraiser_id);
            return ($model)?ucfirst($model->title):'';
        }
    }
}
