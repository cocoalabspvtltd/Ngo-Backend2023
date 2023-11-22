<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\AgencyLandingPage */

$this->title = 'Create Agency Landing Page';
$this->params['breadcrumbs'][] = ['label' => 'Agency Landing Pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="agency-landing-page-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
