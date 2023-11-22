<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\FundraiserSchemeCommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fundraiser Scheme Comments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fundraiser-scheme-comment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- <p> -->
        <?php //= Html::a('Create Fundraiser Scheme Comment', ['create'], ['class' => 'btn btn-success']) ?>
    <!-- </p> -->

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'exportConfig' => [
            GridView::CSV => ['label' => 'Export as CSV', 'filename' => 'Fundraiser-scheme-comment-'.date('d-M-Y')],
            GridView::HTML => ['label' => 'Export as HTML', 'filename' => 'Fundraiser-scheme-comment -'.date('d-M-Y')],
            GridView::EXCEL=> ['label' => 'Export as EXCEL', 'filename' => 'Fundraiser-scheme-comment -'.date('d-M-Y')],
            GridView::TEXT=> ['label' => 'Export as TEXT', 'filename' => 'Fundraiser-scheme-comment -'.date('d-M-Y')],
            GridView::PDF => [
                'filename' => 'Fundraiser-scheme-comment -'.date('d-M-Y'),
                'config' => [
                    'options' => [
                        'title' => 'Fundraiser-scheme-comment -'.date('d-M-Y'),
                        'subject' =>'Fundraiser-scheme-comment -'.date('d-M-Y'),
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
                'attribute' =>'user_id',
                'format' => 'raw',
                'value'=>function ($model) {
                    return $model->getUser();
                },
            ],
            'comment:ntext',
            [
                'attribute' =>'fundraiser_id',
                'format' => 'raw',
                'value'=>function ($model) {
                    return $model->getFundraiser();
                },
            ],
            [
                'attribute' =>'created_at',
                'format' => 'raw',
                'value'=>function ($model) {
                    return date('d M Y h:i a',strtotime($model->created_at));
                },
            ],

            ['class' => 'yii\grid\ActionColumn','template'=>'{delete}'],
        ],
    ]); ?>


</div>
