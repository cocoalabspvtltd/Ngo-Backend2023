<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
$list = [
    '0' => 'Disable',
    '1' => 'Enable'
];
$healthLList = [
    '0' => 'No',
    '1' => 'Yes'
];
/* @var $this yii\web\View */
/* @var $model backend\models\Campaign */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="campaign-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'icon_url')->fileInput() ?>
    <?php if($model->id){ ?>
        <a href="<?php echo $model->getImage()?>" target="_blank">
            <img src="<?php echo $model->getImage()?>" alt="Image" style="height:100px;width:100px;">
        </a>
    <?php }?> 
    <?php if($model->id){ ?>
        <?= $form->field($model, 'campaign_status')->dropdownList($list) ?>
    <?php }?>
    <?= $form->field($model, 'is_health_case')->dropdownList($healthLList) ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
