<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\RelationMaster */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Relation Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="relation-master-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'relation_status',
            'status',
            'created_at',
            'modified_at',
        ],
    ]) ?>

</div>
