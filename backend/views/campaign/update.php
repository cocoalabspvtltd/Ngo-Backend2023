<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Campaign */

$this->title = 'Update Campaign: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Campaigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="campaign-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
