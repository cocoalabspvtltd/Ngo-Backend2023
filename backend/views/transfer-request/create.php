<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\TransferRequest */

$this->title = 'Create Transfer Request';
$this->params['breadcrumbs'][] = ['label' => 'Transfer Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transfer-request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
