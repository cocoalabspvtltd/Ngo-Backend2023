<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\FundraiserComment */

$this->title = 'Create Fundraiser Comment';
$this->params['breadcrumbs'][] = ['label' => 'Fundraiser Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fundraiser-comment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
