<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\FundraiserScheme */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Campaigns List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="fundraiser-scheme-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
            'story:ntext',
            'name',
            'email',
            'phone_number',
            'country_code',
            [
                'attribute' =>'relation_master_id',
                'format' => 'raw',
                'value'=>function ($model) {
                    return $model->getRelationMaster();
                },
            ],
            'patient_name',
            'health_issue',
            'hospital',
            'city',
            'beneficiary_account_name',
            'beneficiary_account_number',
            'beneficiary_bank',
            'beneficiary_ifsc',
            [
                'attribute' =>'beneficiary_image',
                'format' => 'raw',
                'value'=>function ($model) {
                    return '<a href="'.$model->getImage().'" target="_blank">
                    <img src="'.$model->getBeneficiaryImage().'" alt="Image" style="height:100px;width:100px;">
                    </a>';
                },
            ],
            [
                'attribute' =>'documents',
                'format' => 'raw',
                'value'=>function ($model) {
                    return $this->render('/common/images',['model'=>$model]);
                },
            ],
            [
                'attribute' =>'pricing_id',
                'format' => 'raw',
                'value'=>function ($model) {
                    return $model->getPricing();
                },
            ],
            'virtual_account_number',
            'virtual_account_ifsc',
            'virtual_account_name',
            'virtual_account_type'
        ],
    ]) ?>
    <?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
</div>
