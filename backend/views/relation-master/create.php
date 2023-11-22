<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\RelationMaster */

$this->title = 'Create Relation Master';
$this->params['breadcrumbs'][] = ['label' => 'Relation Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="relation-master-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
