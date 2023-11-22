<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\LoanDonation */

$this->title = 'Create Loan Donation';
$this->params['breadcrumbs'][] = ['label' => 'Loan Donations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-donation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
