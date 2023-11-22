<?php

namespace backend\models;

use Yii;
use yii\helpers\Url;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "media".
 *
 * @property int $id
 * @property string|null $heading
 * @property string|null $title
 * @property string|null $description
 * @property string|null $image_url
 * @property string|null $link
 * @property int $status
 * @property string $created_at
 * @property string $modified_at
 */
class Media extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'media';
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
            [['description', 'image_url'], 'string'],
            [['status'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['heading', 'title', 'link'], 'string', 'max' => 255],
            [['heading'], 'unique'],
            [['heading','title','description','link'],'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'heading' => 'Heading',
            'title' => 'Title',
            'description' => 'Description',
            'image_url' => 'Image',
            'link' => 'Link',
            'status' => 'Status',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
    public function getImage(){
        $imagePath = $this->image_url;
        $locationPath = Yii::$app->params['base_path_media_images'];
        $path = $locationPath.$imagePath;
        return Url::to($path);
    }
}
