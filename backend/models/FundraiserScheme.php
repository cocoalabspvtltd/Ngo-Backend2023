<?php

namespace backend\models;
use yii\helpers\Url;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "fundraiser_scheme".
 *
 * @property int $id
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
 */
class FundraiserScheme extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fundraiser_scheme';
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
    public $amount,$documents,$from_date,$to_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           
            [['campaign_id', 'status', 'relation_master_id','is_amount_collected','is_approved','pricing_id','fund_transfered'], 'integer'],
            [['title', 'story','content_title','health_issue', 'hospital', 'beneficiary_account_name', 'beneficiary_account_number', 'beneficiary_bank', 'beneficiary_ifsc', 'beneficiary_image'], 'string'],
            [['country_code','fund_raised'], 'number'],
            [['closing_date', 'created_at', 'modified_at','virtual_account_id','virtual_account_number','virtual_account_name','virtual_account_type','virtual_account_ifsc'], 'safe'],
            [['image_url', 'name', 'email', 'phone_number', 'patient_name', 'city'], 'string', 'max' => 255],
            [['title','fund_required','closing_date','campaign_id','story'],'required'],
            [['phone_number','country_code','beneficiary_account_number'],'number'],
            [['title'],'is_unique'],
            [['email'],'string'],
            [['image_url','beneficiary_image'], 'file', 'maxFiles' => 1,'extensions' => 'png, jpg, jpeg'],
            [['documents'], 'file', 'maxFiles' => 8,'extensions' => 'png, jpg, jpeg, pdf, docx, xlsx'],
            [['phone_number'],'string','max'=>15,'min'=>8],
            [['country_code'],'string','max'=>4,'min'=>2],
            [['fund_required'],'number', 'numberPattern' => '/^\s*[-+]?[0-9]*[,]?[0-9]+([eE][-+]?[0-9]+)?\s*$/'],
            [['name','patient_name','beneficiary_account_name'],'match','pattern' => '/^[a-zA-Z\s]+$/',],
            [['beneficiary_ifsc'],'match','pattern' => '/^[A-Z]{4}[0]{1}[0-9]{5}[a-zA-Z0-9]{1}$/'],
            [['beneficiary_account_number'],'string','min'=>'11','max'=>'16']
        ];
    }
    public function is_unique($attribute){
        $title = $this->title;
        $query = static::find()->where(['title'=>$title])->andWhere(['status' => 1]);
        if($this->id) {
            $query->andWhere(['!=','id',$this->id]);
        }
        $err  = $query->one()!=null?true:false;
        if($err)
            $this->addError($attribute, Yii::t('app', ' Title "'.$title. '" has already been taken.'));
            return $err;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'campaign_id' => 'Purpose Of Raising Fund',
            'image_url' => 'Main Image',
            'title' => 'Title',
            'fund_required' => 'Fund Required',
            'closing_date' => 'Closing Date',
            'story' => 'Story',
            'content_title' => 'Content Title',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'name' => 'Name',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'country_code' => 'Country Code',
            'relation_master_id' => 'Relation',
            'patient_name' => 'Patient Name',
            'health_issue' => 'Health Issue',
            'hospital' => 'Hospital',
            'city' => 'City',
            'beneficiary_account_name' => 'Account Holder Name',
            'beneficiary_account_number' => 'Account Number',
            'beneficiary_bank' => 'Bank',
            'beneficiary_ifsc' => 'IFSC Code',
            'beneficiary_image' => 'Beneficiary Image',
            'is_approved' => 'Status',
            'pricing_id' => 'Pricing',
            'fund_transfered' => 'Fund Transfered'
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
}
