<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
$list = [
    '0' => 'Disable',
    '1' => 'Enable'
];

/* @var $this yii\web\View */
/* @var $model backend\models\RelationMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="relation-master-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php if($model->id){ ?>
        <?= $form->field($model, 'relation_status')->dropdownList($list) ?>
    <?php }?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
