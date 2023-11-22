<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\PricingMaster */

$this->title = 'Update Pricing: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Pricing Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pricing-master-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
