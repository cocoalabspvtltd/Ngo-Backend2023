<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\TransferRequest */

$this->title = 'Update Transfer Request: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Transfer Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="transfer-request-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
