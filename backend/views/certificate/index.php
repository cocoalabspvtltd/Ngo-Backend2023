<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\CertificateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Certificates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="certificate-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'exportConfig' => [
            GridView::CSV => ['label' => 'Export as CSV', 'filename' => '80G-form-requests-'.date('d-M-Y')],
            GridView::HTML => ['label' => 'Export as HTML', 'filename' => '80G-form-requests -'.date('d-M-Y')],
            GridView::EXCEL=> ['label' => 'Export as EXCEL', 'filename' => '80G-form-requests -'.date('d-M-Y')],
            GridView::TEXT=> ['label' => 'Export as TEXT', 'filename' => '80G-form-requests -'.date('d-M-Y')],
            GridView::PDF => [
                'filename' => '80G-form-requests -'.date('d-M-Y'),
                'config' => [
                    'options' => [
                        'title' => '80G-form-requests -'.date('d-M-Y'),
                        'subject' =>'80G-form-requests -'.date('d-M-Y'),
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

            'name',
            'phone_number',
            'pan_number',
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getUser();
                }
            ],
            [
                'attribute' => 'fundraiser_id',
                'format' => 'raw',
                'value' => function($model){
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
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{mybutton}',
                'buttons'=>[
                    'mybutton' => function($url,$model,$key)
                    {
                        return Html::a('<button class="btn btn-primary" type="submit">Generate Certificate</button>',Url::to(['certificate/certificate/?id='.$model->id]));
                    }
                    ]
                    ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
