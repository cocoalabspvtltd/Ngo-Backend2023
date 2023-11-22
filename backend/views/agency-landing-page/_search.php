<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use backend\models\Agency;
use backend\models\FundraiserScheme;
use kartik\date\DatePicker;

$agency = Agency::find()->where(['status'=>1])->all();
$fundraiser = FundraiserScheme::find()->where(['status'=>1])->all();

$agencyList = ArrayHelper::map($agency,'id','name');
$fundraiserList = ArrayHelper::map($fundraiser,'id','title');

/* @var $this yii\web\View */
/* @var $model backend\models\AgencyLandingPageSearch */
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
<div class="agency-landing-page-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'agency_id')->dropdownList($agencyList,['prompt'=>'Select One...']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'fundraiser_scheme_id')->dropdownList($fundraiserList,['prompt'=>'Select One...']) ?>
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
