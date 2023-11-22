<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\FundraiserSchemeUpdateRequest */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Fundraiser Scheme Update Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="fundraiser-scheme-update-request-view">

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
                    return $this->render('/common/image',['model'=>$model,'fieldName'=>'beneficiary_image']);
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
            'virtual_account_number',
            'virtual_account_ifsc',
            'virtual_account_name',
            'virtual_account_type'
        ],
    ]) ?>
    <?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>

</div>
