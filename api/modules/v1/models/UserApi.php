<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "user_api".
 *
 * @property int $id
 * @property string $api_token
 * @property string $user_token
 * @property int $user_id
 * @property string $ts_expiry
 * @property string $date_added
 * @property int $status
 * @property float|null $phone
 * @property int|null $code
 */
class UserApi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_api';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['api_token', 'user_token', 'user_id', 'ts_expiry'], 'required'],
            [['user_id', 'status', 'code'], 'integer'],
            [['ts_expiry', 'date_added'], 'safe'],
            [['phone'], 'number'],
            [['api_token'], 'string', 'max' => 2555],
            [['user_token'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'api_token' => 'Api Token',
            'user_token' => 'User Token',
            'user_id' => 'User ID',
            'ts_expiry' => 'Ts Expiry',
            'date_added' => 'Date Added',
            'status' => 'Status',
            'phone' => 'Phone',
            'code' => 'Code',
        ];
    }
}
