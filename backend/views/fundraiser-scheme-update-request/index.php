<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\FundraiserSchemeUpdateRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fundraiser Scheme Update Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fundraiser-scheme-update-request-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'exportConfig' => [
            GridView::CSV => ['label' => 'Export as CSV', 'filename' => 'Fundraiser-scheme-'.date('d-M-Y')],
            GridView::HTML => ['label' => 'Export as HTML', 'filename' => 'Fundraiser-scheme -'.date('d-M-Y')],
            GridView::EXCEL=> ['label' => 'Export as EXCEL', 'filename' => 'Fundraiser-scheme -'.date('d-M-Y')],
            GridView::TEXT=> ['label' => 'Export as TEXT', 'filename' => 'Fundraiser-scheme -'.date('d-M-Y')],
            GridView::PDF => [
                'filename' => 'Fundraiser-scheme -'.date('d-M-Y'),
                'config' => [
                    'options' => [
                        'title' => 'Fundraiser-scheme -'.date('d-M-Y'),
                        'subject' =>'Fundraiser-scheme -'.date('d-M-Y'),
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

            'title:ntext',
            [
                'attribute' =>'created_by',
                'format' => 'raw',
                'value'=>function ($model) {
                    return $model->getUser();
                },
            ],
            [
                'attribute' =>'campaign_id',
                'format' => 'raw',
                'value'=>function ($model) {
                    return $model->getCampaign();
                },
            ],
            'fund_required',
            [
                'attribute' =>'closing_date',
                'format' => 'raw',
                'value'=>function ($model) {
                    return date('d M Y',strtotime($model->closing_date));
                },
            ],
            [
                'attribute' => 'is_approved',
                'format' => 'raw',
                'value' => function($model){
                    $url = Url::to(['fundraiser-scheme-update-request/approve','id'=>$model->id]);
                    if($model->is_approved == 1){
                        return '<button class="btn btn-success">Approved</button>';
                    }else{
                        return '<a href="'.$url.'" class="btn btn-success">Approve</a>';
                    }
                }
            ],
            [
                'attribute' => 'is_approved',
                'format' => 'raw',
                'value' => function($model){
                    $rejUrl = Url::to(['fundraiser-scheme-update-request/reject','id'=>$model->id]);
                    if($model->is_approved == -1){
                        return '<button class="btn btn-danger">Rejected</button>';
                    }else{
                        return '<a href="'.$rejUrl.'" class="btn btn-danger">Reject</a>';
                    }
                }
            ],
            [
                'attribute' => 'comment',
                'format' => 'raw',
                'value' => function($model){
                    $url = Url::to(['fundraiser-scheme-update-request/comment','id'=>$model->id]);
                    return '<a href="'.$url.'" class="btn btn-primary">Comment</a>';
                }
            ],

            ['class' => 'yii\grid\ActionColumn','template'=>'{view}'],
        ],
    ]); ?>


</div>
