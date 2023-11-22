<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TransferRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Transfer Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transfer-request-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'exportConfig' => [
            GridView::CSV => ['label' => 'Export as CSV', 'filename' => 'Transfer-request-'.date('d-M-Y')],
            GridView::HTML => ['label' => 'Export as HTML', 'filename' => 'Transfer-request -'.date('d-M-Y')],
            GridView::EXCEL=> ['label' => 'Export as EXCEL', 'filename' => 'Transfer-request -'.date('d-M-Y')],
            GridView::TEXT=> ['label' => 'Export as TEXT', 'filename' => 'Transfer-request -'.date('d-M-Y')],
            GridView::PDF => [
                'filename' => 'Transfer-request -'.date('d-M-Y'),
                'config' => [
                    'options' => [
                        'title' => 'Transfer-request -'.date('d-M-Y'),
                        'subject' =>'Transfer-request -'.date('d-M-Y'),
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
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => function ($model){
                    return $model->getUser();
                }
            ],
            [
                'attribute' => 'fundraiser_id',
                'format' => 'raw',
                'value' => function ($model){
                    return $model->getFundraiser();
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function ($model){
                    return date('d M Y',strtotime($model->created_at));
                }
            ],
            'amount',
            'transferred_amount',
            'deducted_amount'
        ],
    ]); ?>


</div>
