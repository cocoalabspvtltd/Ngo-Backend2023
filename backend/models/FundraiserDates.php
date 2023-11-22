<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "fundraiser_dates".
 *
 * @property int $id
 * @property string $start_date
 * @property string $closing_date
 * @property int $no_of_days
 */
class FundraiserDates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fundraiser_dates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start_date', 'closing_date', 'no_of_days'], 'required'],
            [['start_date', 'closing_date'], 'safe'],
            [['fundraiser_id','no_of_days'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fundraiser_id' => 'Fundraiser ID',
            'start_date' => 'Start Date',
            'closing_date' => 'Closing Date',
            'no_of_days' => 'No Of Days',
        ];
    }
}
