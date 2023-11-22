<!--<!DOCTYPE html>-->
<!--<html>-->
<!--<style>-->
<!--table, th, td {-->
<!--  border:1px solid black;-->
<!--}-->
<!--th-->
<!--{-->
<!--    background-color : #B2BEB5;-->
<!--}-->
<!--</style>-->
<!--<body>-->

<!--<h2>All Donations</h2>-->

<!--<table style="width:100%">-->
<!--  <tr>-->
    <!--<th>Fundraiser Scheme</th>-->
    <!--<th>Donated By</th>-->
<!--    <th>Name</th>-->
<!--    <th>Email</th>-->
<!--    <th>Amount</th>-->
<!--    <th>Anonymous or Not</th>-->
<!--    <th>Date</th>-->
<!--  </tr>-->

<!--foreach($donation_list as $key =>$items)-->
<!--{-->
<!--    ?>-->
<!--  <tr>-->
    <!--<td><?//php echo $fundraiser_name ?></td>-->
    <!--<td><?//php echo $donated_by ?></td>-->
<!--    <td><?//php echo $items->name ?></td>-->
<!--    <td><?//php echo $items->email ?></td>-->
<!--     <td><?//php echo $items->amount ?></td>-->
<!--     <td><?//php echo $items->show_donor_information ?></td>-->
<!--    <td><?//php echo $items->created_at ?></td>-->
   
<!--  </tr>-->

<!--?>-->
 
<!--</table>-->


<!--</body>-->
<!--</html>-->

<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DonationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Donations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="donation-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'exportConfig' => [
            GridView::CSV => ['label' => 'Export as CSV', 'filename' => 'Donation-'.date('d-M-Y')],
            GridView::HTML => ['label' => 'Export as HTML', 'filename' => 'Donation -'.date('d-M-Y')],
            GridView::EXCEL=> ['label' => 'Export as EXCEL', 'filename' => 'Donation -'.date('d-M-Y')],
            GridView::TEXT=> ['label' => 'Export as TEXT', 'filename' => 'Donation -'.date('d-M-Y')],
            GridView::PDF => [
                'filename' => 'Donation -'.date('d-M-Y'),
                'config' => [
                    'options' => [
                        'title' => 'Donation -'.date('d-M-Y'),
                        'subject' =>'Donation -'.date('d-M-Y'),
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
                'attribute' =>'fundraiser_id',
                'format' => 'raw',
                'value'=>function ($model) {
                    return $model->getFundraiser();
                },
            ],
              [
                'attribute' =>'fundraiser_id',
                'format' => 'raw',
                'value'=>function ($model) {
                    return $model->getFundraiserName();
                },
            ],
            [
                'attribute' =>'donated_by',
                'format' => 'raw',
                'value'=>function ($model) {
                    return $model->getUser();
                },
            ],
            'name',
            'email:email',
            'amount',
            [
                'attribute' =>'show_donor_information',
                'format' => 'raw',
                'value'=>function ($model) {
                    return ($model->show_donor_information == 1)?'Yes':'No';
                },
            ],
            [
                'attribute' =>'created_at',
                'format' => 'raw',
                'value'=>function ($model) {
                    return date('d M Y',strtotime($model->created_at));
                },
            ],
        ],
    ]); ?>


</div>


