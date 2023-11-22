<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\Campaign;
use backend\models\User;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\FundraiserSchemeSearch */
/* @var $form yii\widgets\ActiveForm */
$campaign = Campaign::find()->where(['status'=>1])->all();
$campaignList = ArrayHelper::map($campaign,'id','title');
$user = User::find()->where(['status'=>1])->all();
$userList = ArrayHelper::map($user,'id','name');
$list = array(
    '0' => 'Pending',
    '1' => 'Approved',
    '-1' => 'Rejected',
    '2' => 'Date Closed',
    '3' => 'Fund Raised'
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
<div class="fundraiser-scheme-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'campaign_id')->dropDownList($campaignList,['prompt'=>'Select One...']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'title') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'created_by')->dropDownList($userList,['prompt'=>'Select One...']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'is_approved')->dropDownList($list,['prompt'=>'Select One...']) ?>
        </div>
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
