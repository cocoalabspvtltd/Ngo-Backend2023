<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use backend\models\Agency;
use backend\models\FundraiserScheme;

$agency = Agency::find()->where(['status'=>1])->all();
$fundraiser = FundraiserScheme::find()->where(['status'=>1])->all();

$agencyList = ArrayHelper::map($agency,'id','name');
$fundraiserList = ArrayHelper::map($fundraiser,'id','title');

/* @var $this yii\web\View */
/* @var $model backend\models\AgencyLandingPage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="agency-landing-page-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'agency_id')->dropdownList($agencyList,['prompt'=>'Select One...']) ?>

    <?= $form->field($model, 'fundraiser_scheme_id')->dropdownList($fundraiserList,['prompt'=>'Select One...']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('cancel', ['index'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
