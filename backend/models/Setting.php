<?php

namespace backend\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property string|null $facebook
 * @property string|null $instagram
 * @property string|null $whatsapp
 * @property string|null $linkedin
 * @property string|null $twitter
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class Setting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'setting';
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
            [['created_at', 'modified_at','contact_us_title','address','google_map_key','visitors_count'], 'safe'],
            [['facebook', 'instagram', 'whatsapp', 'linkedin', 'twitter','email','latitude','longitude'], 'string', 'max' => 255],
            [['facebook', 'instagram', 'whatsapp', 'linkedin', 'twitter','contact_us_title','address','email','latitude','longitude','google_map_key'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'whatsapp' => 'Whatsapp',
            'linkedin' => 'Linkedin',
            'twitter' => 'Twitter',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
