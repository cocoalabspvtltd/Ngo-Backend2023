<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\FundraiserSchemeUpdateRequest */

$this->title = 'Update Fundraiser Scheme Update Request: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Fundraiser Scheme Update Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fundraiser-scheme-update-request-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
