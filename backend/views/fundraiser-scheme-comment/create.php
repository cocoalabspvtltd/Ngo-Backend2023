<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\FundraiserSchemeComment */

$this->title = 'Create Fundraiser Scheme Comment';
$this->params['breadcrumbs'][] = ['label' => 'Fundraiser Scheme Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fundraiser-scheme-comment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
