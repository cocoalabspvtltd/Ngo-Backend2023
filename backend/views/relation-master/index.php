<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\RelationMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Relation Master';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="relation-master-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Relation', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'exportConfig' => [
            GridView::CSV => ['label' => 'Export as CSV', 'filename' => 'Relation-'.date('d-M-Y')],
            GridView::HTML => ['label' => 'Export as HTML', 'filename' => 'Relation -'.date('d-M-Y')],
            GridView::EXCEL=> ['label' => 'Export as EXCEL', 'filename' => 'Relation -'.date('d-M-Y')],
            GridView::TEXT=> ['label' => 'Export as TEXT', 'filename' => 'Relation -'.date('d-M-Y')],
            GridView::PDF => [
                'filename' => 'Relation -'.date('d-M-Y'),
                'config' => [
                    'options' => [
                        'title' => 'Relation -'.date('d-M-Y'),
                        'subject' =>'Relation -'.date('d-M-Y'),
                        'keywords' => 'pdf, export, other, keywords, here'
                    ],
                ]
            ],
        ],
        'containerOptions' => ['style'=>'overflow: auto'],
        'toolbar' =>  [
            '{export}',
            '{toggleData}'
        ],
        'pjax' => false,
        'bordered' => true,
        'striped' => false,
        'condensed' => false,
        'responsive' => true,
        'hover' => true,
        'floatHeader' => true,
        'floatHeaderOptions' => ['scrollingTop' => 10],
        'showPageSummary' => true,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY
        ],
        'pager' => [
            'firstPageLabel' => 'First',
            'lastPageLabel'  => 'Last'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' =>'title',
                'format' => 'raw',
                'value'=>function ($model) {
                    return ucfirst($model->title);
                },
            ],
            [
                'attribute' =>'relation_status',
                'format' => 'raw',
                'value'=>function ($model) {
                    return ($model->relation_status == 1)?'Enabled':'Disabled';
                },
            ],

            ['class' => 'yii\grid\ActionColumn','template'=>'{update}  {delete}'],
        ],
    ]); ?>


</div>
