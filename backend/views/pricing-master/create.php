<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\PricingMaster */

$this->title = 'Create Pricing';
$this->params['breadcrumbs'][] = ['label' => 'Pricing Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pricing-master-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
