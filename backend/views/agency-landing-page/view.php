<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\AgencyLandingPage */

$this->title = $model->getAgency();
$this->params['breadcrumbs'][] = ['label' => 'Agency Landing Pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="agency-landing-page-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
            'virtual_account_id',
            'virtual_account_number',
            'virtual_account_name',
            'virtual_account_type',
            'virtual_account_ifsc',
            'total_amount_collected',
        ],
    ]) ?>

    
    <?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>

</div>
