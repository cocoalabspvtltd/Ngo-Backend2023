<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Donation */

$this->title = 'Create Donation';
$this->params['breadcrumbs'][] = ['label' => 'Donations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="donation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
