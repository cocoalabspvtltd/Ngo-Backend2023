<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\LoanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Loans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Loan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'exportConfig' => [
            GridView::CSV => ['label' => 'Export as CSV', 'filename' => 'Loan-'.date('d-M-Y')],
            GridView::HTML => ['label' => 'Export as HTML', 'filename' => 'Loan -'.date('d-M-Y')],
            GridView::EXCEL=> ['label' => 'Export as EXCEL', 'filename' => 'Loan -'.date('d-M-Y')],
            GridView::TEXT=> ['label' => 'Export as TEXT', 'filename' => 'Loan -'.date('d-M-Y')],
            GridView::PDF => [
                'filename' => 'Loan -'.date('d-M-Y'),
                'config' => [
                    'options' => [
                        'title' => 'Loan -'.date('d-M-Y'),
                        'subject' =>'Loan -'.date('d-M-Y'),
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
            'title',
            'purpose:ntext',
            'amount',
            'location:ntext',
            'description:ntext',
            [
                'attribute' =>'image',
                'format' => 'raw',
                'value'=>function ($model) {
                    return $this->render('/common/image',['model'=>$model,'fieldName'=>'image_url']);
                },
            ],
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
                    $url = Url::to(['loan/approve','id'=>$model->id]);
                    if($model->is_approved == 0){
                        return '<a href="'.$url.'" class="btn btn-success">Approve</a>';
                    }else{
                        return '<a href="'.$url.'" class="btn btn-danger">Reject</a>';
                    }
                }
            ],
            //'status',
            //'created_at',
            //'modified_at',

            ['class' => 'yii\grid\ActionColumn','template'=>'{update} {delete}'],
        ],
    ]); ?>


</div>
