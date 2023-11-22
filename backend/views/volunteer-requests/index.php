<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\VolunteerRequestsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Volunteer Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="volunteer-requests-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'exportConfig' => [
            GridView::CSV => ['label' => 'Export as CSV', 'filename' => 'Volunteer-request-'.date('d-M-Y')],
            GridView::HTML => ['label' => 'Export as HTML', 'filename' => 'Volunteer-request -'.date('d-M-Y')],
            GridView::EXCEL=> ['label' => 'Export as EXCEL', 'filename' => 'Volunteer-request -'.date('d-M-Y')],
            GridView::TEXT=> ['label' => 'Export as TEXT', 'filename' => 'Volunteer-request -'.date('d-M-Y')],
            GridView::PDF => [
                'filename' => 'Volunteer-request -'.date('d-M-Y'),
                'config' => [
                    'options' => [
                        'title' => 'Volunteer-request -'.date('d-M-Y'),
                        'subject' =>'Volunteer-request -'.date('d-M-Y'),
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
            'address:ntext',
            'phone_number',
            'email:email',
        ],
    ]); ?>


</div>
