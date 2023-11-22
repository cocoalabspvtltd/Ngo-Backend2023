<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\FundraiserSchemeUpdateRequest */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fundraiser-scheme-update-request-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fundraiser_id')->textInput() ?>

    <?= $form->field($model, 'campaign_id')->textInput() ?>

    <?= $form->field($model, 'image_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'fund_required')->textInput() ?>

    <?= $form->field($model, 'closing_date')->textInput() ?>

    <?= $form->field($model, 'story')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'modified_at')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'country_code')->textInput() ?>

    <?= $form->field($model, 'relation_master_id')->textInput() ?>

    <?= $form->field($model, 'patient_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'health_issue')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'hospital')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'beneficiary_account_name')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'beneficiary_account_number')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'beneficiary_bank')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'beneficiary_ifsc')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'beneficiary_image')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'pricing_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
