<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use backend\models\User;
use yii\helpers\ArrayHelper;

$users = User::find()->where(['status'=>1,'role'=>'campaigner'])->all();
$list = ArrayHelper::map($users,'id','name');

/* @var $this yii\web\View */
/* @var $model backend\models\Loan */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="loan-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'created_by')->dropdownList($list,['prompt'=>'Select','maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'purpose')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'location')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php
        if($model->id){
            $model->closing_date = date('d-m-Y',strtotime($model->closing_date));
        }
        echo $form->field($model, 'closing_date')->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Enter closing date ...'],
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'dd-mm-yyyy',
                'startDate' => 'd'
            ]
        ]); 
    ?>

    <?= $form->field($model, 'image_url')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
