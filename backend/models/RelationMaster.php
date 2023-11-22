<?php

namespace backend\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "relation_master".
 *
 * @property int $id
 * @property string|null $title
 * @property int $relation_status
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class RelationMaster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'relation_master';
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
            [['relation_status', 'status'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['title'],'required'],
            [['title'],'is_unique'],
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
            'relation_status' => 'Relation Status',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
