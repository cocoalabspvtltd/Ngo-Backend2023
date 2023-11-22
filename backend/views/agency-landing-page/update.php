<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\AgencyLandingPage */

$this->title = 'Update Agency Landing Page: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Agency Landing Pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="agency-landing-page-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
