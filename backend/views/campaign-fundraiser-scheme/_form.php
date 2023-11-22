<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use backend\models\Campaign;
use backend\models\RelationMaster;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\FundraiserScheme */
/* @var $form yii\widgets\ActiveForm */
$campaign = Campaign::findAll(['status'=>1]);
$campaignList = ArrayHelper::map($campaign,'id','title');

$relation = RelationMaster::findAll(['status'=>1]);
$relationList = ArrayHelper::map($relation,'id','title');
?>

<div class="fundraiser-scheme-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <?= $form->field($model, 'phone_number')->textInput() ?>

    <?= $form->field($model, 'country_code')->textInput() ?>

    <?= $form->field($model, 'relation_master_id')->dropdownList($relationList,['prompt'=>'Select One........']); ?>

    <?= $form->field($model, 'patient_name')->textInput() ?>

    <?= $form->field($model, 'health_issue')->textInput() ?>
    
    <?= $form->field($model, 'hospital')->textInput() ?>

    <?= $form->field($model, 'city')->textInput() ?>

    <?= $form->field($model, 'beneficiary_account_name')->textInput() ?>

    <?= $form->field($model, 'beneficiary_account_number')->textInput() ?>

    <?= $form->field($model, 'beneficiary_bank')->textInput() ?>

    <?= $form->field($model, 'beneficiary_ifsc')->textInput() ?>

    <?= $form->field($model, 'campaign_id')->dropdownList($campaignList,['prompt'=>'Select One........']); ?>

    <?= $form->field($model, 'image_url')->fileInput() ?>
    <?php if($model->id){ ?>
        <a href="<?php echo $model->getImage()?>" target="_blank">
            <img src="<?php echo $model->getImage()?>" alt="Image" style="height:100px;width:100px;">
        </a>
    <?php }?>

    <?= $form->field($model, 'beneficiary_image')->fileInput() ?>
    <?php if($model->id){ ?>
        <a href="<?php echo $model->getBeneficiaryImage()?>" target="_blank">
            <img src="<?php echo $model->getBeneficiaryImage()?>" alt="Image" style="height:100px;width:100px;">
        </a>
    <?php }?>

    <?= $form->field($model, 'documents[]')->fileInput(['multiple'=>true]) ?>

    <?= $form->field($model, 'title')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'fund_required')->textInput() ?>

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

    <?= $form->field($model, 'story')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
