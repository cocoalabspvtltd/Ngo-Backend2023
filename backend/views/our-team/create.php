<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\OurTeam */

$this->title = 'Create Team Member';
$this->params['breadcrumbs'][] = ['label' => 'Our Teams', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="our-team-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
