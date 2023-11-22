<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\FundraiserScheme */

$this->title = 'Comment Campaign: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Fundraiser Scheme List', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fundraiser-scheme-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="fundraiser-scheme-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($modelFundraiserComment, 'message')->textArea(['rows'=>6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Send', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
