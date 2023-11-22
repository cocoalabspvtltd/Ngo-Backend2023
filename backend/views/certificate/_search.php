<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use backend\models\User;
use backend\models\FundraiserScheme;
use kartik\date\DatePicker;

$user = User::find()->where(['status'=>1])->andWhere(['!=','role','super-admin'])->all();
$userList = ArrayHelper::map($user,'id','name');

$fundraiser = FundraiserScheme::find()->where(['status'=>1])->andWhere(['!=','created_by',Yii::$app->user->identity->id])->all();
$fundraiserList = ArrayHelper::map($fundraiser,'id','title');
/* @var $this yii\web\View */
/* @var $model backend\models\CertificateSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
.datepicker {
    top: 252.2px;
    left: 889.6px;
    z-index: 9999 !important;
    display: block;
}
</style>

<div class="certificate-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'name') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'phone_number') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'pan_number') ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'user_id')->dropdownList($userList,['prompt'=>'Select One...']) ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'fundraiser_id')->dropdownList($fundraiserList,['prompt'=>'Select One...']) ?>
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
