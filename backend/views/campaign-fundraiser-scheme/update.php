<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\FundraiserScheme */

$this->title = 'Update Fundraiser Scheme: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Fundraiser Scheme List', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fundraiser-scheme-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
