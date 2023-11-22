<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\LoanDonation */

$this->title = 'Update Loan Donation: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Loan Donations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="loan-donation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
