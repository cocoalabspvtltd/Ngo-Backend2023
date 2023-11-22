<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\FundraiserSchemeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Campaigns List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fundraiser-scheme-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Campaign', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'exportConfig' => [
            GridView::CSV => ['label' => 'Export as CSV', 'filename' => 'Campaign-list-'.date('d-M-Y')],
            GridView::HTML => ['label' => 'Export as HTML', 'filename' => 'Campaign-list -'.date('d-M-Y')],
            GridView::EXCEL=> ['label' => 'Export as EXCEL', 'filename' => 'Campaign-list -'.date('d-M-Y')],
            GridView::TEXT=> ['label' => 'Export as TEXT', 'filename' => 'Campaign-list -'.date('d-M-Y')],
            GridView::PDF => [
                'filename' => 'Campaign-list -'.date('d-M-Y'),
                'config' => [
                    'options' => [
                        'title' => 'Campaign-list -'.date('d-M-Y'),
                        'subject' =>'Campaign-list -'.date('d-M-Y'),
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
            [
                'attribute' =>'image',
                'format' => 'raw',
                'value'=>function ($model) {
                    return $this->render('/common/image',['model'=>$model,'fieldName'=>'image_url']);
                },
            ],
            'title:ntext',
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
                    $rejUrl = Url::to(['fundraiser-scheme/reject','id'=>$model->id]);
                    if($model->is_approved == -1){
                        return '<button class="btn btn-danger">Cancelled</button>';
                    }else{
                        return '<a href="'.$rejUrl.'" class="btn btn-danger">Cancel</a>';
                    }
                }
            ],
            [
                'attribute' => 'is_approved',
                'format' => 'raw',
                'value' => function($model){
                    $url = Url::to(['fundraiser-scheme/approve','id'=>$model->id]);
                    if($model->is_approved == 1){
                        return '<button class="btn btn-success">Approved</button>';
                    }else{
                        return '<a href="'.$url.'" class="btn btn-success">Approve</a>';
                    }
                }
            ],
            //'story:ntext',
            //'status',
            //'created_at',
            //'modified_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
