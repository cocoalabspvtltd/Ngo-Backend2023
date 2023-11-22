<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\VolunteerRequests */

$this->title = 'Create Volunteer Requests';
$this->params['breadcrumbs'][] = ['label' => 'Volunteer Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="volunteer-requests-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
