<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;;
use yii\widgets\ActiveForm;
use backend\models\User;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\LoanSearch */
/* @var $form yii\widgets\ActiveForm */
$user = User::find()->where(['status'=>1])->all();
$userList = ArrayHelper::map($user,'id','name');
$list = array(
    '0' => 'Pending',
    '1' => 'Approved',
    '-1' => 'Rejected'
);
?>
<style>
.datepicker {
    top: 252.2px;
    left: 889.6px;
    z-index: 9999 !important;
    display: block;
}
</style>

<div class="loan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'created_by')->dropdownList($userList,['prompt'=>'Select One...']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'title') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'purpose') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'amount') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'location') ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'description') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'is_approved')->dropDownList($list,['prompt'=>'Select One...']) ?>
        </div>
    </div>
    <div class="row">
        <?php 
        if($model->from_date){
            $model->from_date = date('d-m-Y',strtotime($model->from_date));
        }
        if($model->to_date){
            $model->to_date = date('d-m-Y',strtotime($model->to_date));
        }
        ?>
        <div class="col-md-3">
            <?php echo $form->field($model, 'from_date')->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => 'Select Date...'],
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd-mm-yyyy',
                        'endDate' => 'd'
                    ]
            ]); ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'to_date')->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => 'Select Date...'],
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd-mm-yyyy',
                        'endDate' => 'd'
                    ]
            ]); ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-danger','style'=>'color: black']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
