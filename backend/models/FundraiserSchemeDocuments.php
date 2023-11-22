<?php

namespace backend\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "fundraiser_scheme_documents".
 *
 * @property int $id
 * @property int|null $fundraiser_scheme_id
 * @property string|null $doc_url
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class FundraiserSchemeDocuments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fundraiser_scheme_documents';
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
            [['fundraiser_scheme_id', 'status'], 'integer'],
            [['doc_url'], 'string'],
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
            'fundraiser_scheme_id' => 'Fundraiser Scheme ID',
            'doc_url' => 'Doc Url',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
