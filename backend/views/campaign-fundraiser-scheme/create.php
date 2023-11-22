<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\FundraiserScheme */

$this->title = 'Create Campaign';
$this->params['breadcrumbs'][] = ['label' => 'Campaigns List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fundraiser-scheme-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
