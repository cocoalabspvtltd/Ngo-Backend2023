<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AgencyLandingPageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Agency Landing Pages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="agency-landing-page-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Agency Landing Page', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'exportConfig' => [
            GridView::CSV => ['label' => 'Export as CSV', 'filename' => 'Agency-landing-pages-'.date('d-M-Y')],
            GridView::HTML => ['label' => 'Export as HTML', 'filename' => 'Agency-landing-pages -'.date('d-M-Y')],
            GridView::EXCEL=> ['label' => 'Export as EXCEL', 'filename' => 'Agency-landing-pages -'.date('d-M-Y')],
            GridView::TEXT=> ['label' => 'Export as TEXT', 'filename' => 'Agency-landing-pages -'.date('d-M-Y')],
            GridView::PDF => [
                'filename' => 'Agency-landing-pages -'.date('d-M-Y'),
                'config' => [
                    'options' => [
                        'title' => 'Agency-landing-pages -'.date('d-M-Y'),
                        'subject' =>'Agency-landing-pages -'.date('d-M-Y'),
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
                'attribute' => 'agency_id',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getAgency();
                }
            ],
            [
                'attribute' => 'fundraiser_scheme_id',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getFundraiser();
                }
            ],
            'landing_page_url:url',
            'virtual_account_number',
            'virtual_account_ifsc',

            ['class' => 'yii\grid\ActionColumn','template'=>'{update} {view} {delete} {mybutton}',
            'buttons' =>[
                'mybutton' => function($url,$model,$key){
                    $id=$model->id;
                    $url ="https://www.cocoalabs.in/ngo/agency_mail.php?id=".$id;
                    return Html::a('<button class="btn btn-primary" >Send Mail</button>',$url);
                }
                ]
                ],
        ],
    ]); ?>


</div>
