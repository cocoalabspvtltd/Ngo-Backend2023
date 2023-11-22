<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\OurTeam */

$this->title = 'Update Team Member: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Our Teams', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="our-team-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
