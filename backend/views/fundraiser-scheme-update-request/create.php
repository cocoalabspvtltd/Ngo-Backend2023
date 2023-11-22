<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\FundraiserSchemeUpdateRequest */

$this->title = 'Create Fundraiser Scheme Update Request';
$this->params['breadcrumbs'][] = ['label' => 'Fundraiser Scheme Update Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fundraiser-scheme-update-request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
