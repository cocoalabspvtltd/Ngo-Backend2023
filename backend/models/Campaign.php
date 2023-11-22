<?php

namespace backend\models;
use yii\helpers\Url;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "campaign".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $icon_url
 * @property int $campaign_status
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class Campaign extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'campaign';
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
            [['campaign_status', 'status','is_health_case'], 'integer'],
            [['created_at', 'modified_at','is_health_case'], 'safe'],
            [['title', 'icon_url'], 'string', 'max' => 255],
            [['title'],'required'],
            [['title'],'is_unique'],
            [['icon_url'], 'file', 'maxFiles' => 1,'extensions' => 'png, jpg, jpeg'],
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
            'title' => 'Title',
            'icon_url' => 'Icon',
            'campaign_status' => 'Campaign Status',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    } 
    public function getImage(){
        $imagePath = $this->icon_url;
        $locationPath = Yii::$app->params['base_path_campaign_icons'];
        $path = $locationPath.$imagePath;
        return Url::to($path);
    }
    
   public function getDonations()
    {
        return $this->hasMany(Donation::class, ['campaign_id' => 'id']);
    }
    
}
