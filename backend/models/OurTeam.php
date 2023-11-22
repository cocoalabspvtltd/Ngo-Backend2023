<?php

namespace backend\models;

use Yii;
use yii\helpers\Url;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "our_team".
 *
 * @property int $id
 * @property string|null $employee_name
 * @property string|null $designation
 * @property string|null $image_url
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class OurTeam extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'our_team';
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
    public $from_date,$to_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['employee_name', 'designation', 'image_url'], 'string', 'max' => 255],
            [['employee_name','designation'],'required'],
            [['employee_name'],'unique'],
            [['image_url'], 'file', 'maxFiles' => 1,'extensions' => 'png, jpg, jpeg'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'employee_name' => 'Employee Name',
            'designation' => 'Designation',
            'image_url' => 'Image',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
    public function getImage(){
        $imagePath = $this->image_url;
        $locationPath = Yii::$app->params['base_path_profile_images'];
        $path = $locationPath.$imagePath;
        return Url::to($path);
    }
}
