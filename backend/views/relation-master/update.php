<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\RelationMaster */

$this->title = 'Update Relation Master: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Relation Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="relation-master-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
