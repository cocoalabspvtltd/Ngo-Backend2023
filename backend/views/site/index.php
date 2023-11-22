<?php

/* @var $this yii\web\View */

$this->title = 'NGO';
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<style>
.dashboard-box {
    box-shadow: 0 2px 12px 0 rgb(2 53 77 / 19%);
    border-radius: 10px;
    padding: 30px 20px;
    text-align: center;
    margin: 16px 40px;
    border-style: outset;
}
.text-label {
    margin-top: -18px;
}
</style>
<div class="site-index">
    <div class="jumbotron">
        <p class="lead">Welcome To</p>
        <h1>Crowd Works India Foundation</h1>
    </div>
</div>
<?php 
$role = Yii::$app->user->identity->role;
if($role == 'super-admin')
{
    ?>
<!-- <div class="container">
    <div class="row">   
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <?php 
        $get = Yii::$app->request->get();
        if($get && $get['FundraiserScheme']['from_date']){
            $model->from_date = date('d-m-Y',strtotime($get['FundraiserScheme']['from_date']));
        }
        if($get && $get['FundraiserScheme']['to_date']){
            $model->to_date = date('d-m-Y',strtotime($get['FundraiserScheme']['to_date']));
        }
        ?>
        <div class="col-md-3">
            <?php echo $form->field($model, 'from_date')->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => 'Select Date...'],
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd-mm-yyyy',
                        'endDate' => 'd'
                    ]
            ]); ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'to_date')->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => 'Select Date...'],
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd-mm-yyyy',
                        'endDate' => 'd'
                    ]
            ]); ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div> -->
<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
        <div class="dashboard-box" style="background: white;">
            <div class="row text-label">
                <h3>Number Of Visitors</h3>
            </div>
            <div class="row text-label">
                <h3 id="visitors"><?=$totalVisitors?></h3>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
        <div class="dashboard-box" style="background: white;">
            <div class="row text-label">
                <h3>Number Of Users</h3>
            </div>
            <div class="row text-label">
                <h3 id="users"><?=$totalUsers?></h3>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
        <div class="dashboard-box" style="background: white;">
            <div class="row text-label">
                <h3>Number Of Fundraisers</h3>
            </div>
            <div class="row text-label">
                <h3 id="fundraisers"><?=$totalFundraisers?></h3>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
        <div class="dashboard-box" style="background: white;">
            <div class="row text-label">
                <h3>Number Of Campaigns</h3>
            </div>
            <div class="row text-label">
                <h3 id="campaigns"><?=$totalCampaigns?></h3>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
        <div class="dashboard-box" style="background: white;">
            <div class="row text-label">
                <h3>Total Amount Collected</h3>
            </div>
            <div class="row text-label">
                <h3 id="amount-collected"><?=number_format($totalFundRaised)?></h3>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
        <div class="dashboard-box" style="background: white;">
            <div class="row text-label">
                <h3>Total Amount Required</h3>
            </div>
            <div class="row text-label">
                <h3 id="amount-required"><?=number_format($totalFundRequired)?></h3>
            </div>
        </div>
    </div>
</div>
<?php 

$this->registerJs("
    function animateValue(id, start, end, duration) {
        if (start === end) return;
        var range = end - start;
        var current = start;
        var increment = end > start? 1 : -1;
        var stepTime = Math.abs(Math.floor(duration / range));
        var obj = document.getElementById(id);
        var timer = setInterval(function() {
            current += increment;
            obj.innerHTML = current;
            if (current == end) {
                clearInterval(timer);
            }
        }, stepTime);
    }

    var userCount = '$totalUsers';
    var visitorCount = '$totalVisitors';
    var fundraisersCount = '$totalFundraisers';
    var campaignsCount = '$totalCampaigns';
    var amountCollectedCount = '$totalFundRaised';
    var amountRequiredCount = '$totalFundRequired';

    if(userCount == 0){
        animateValue('users', 0, 0, 500);
    }else{
        animateValue('users', 0, userCount, 500);
    }
    if(visitorCount == 0){
        animateValue('visitors', 0, 0, 500);
    }else{
        animateValue('visitors', 0, visitorCount, 500);
    }
    if(fundraisersCount == 0){
        animateValue('fundraisers', 0, 0, 500);
    }else{
        animateValue('fundraisers', 0, fundraisersCount, 500);
    }
    if(campaignsCount == 0){
        animateValue('campaigns', 0, 0, 500);
    }else{
        animateValue('campaigns', 0, campaignsCount, 500);
    }
    // animateValue('amount-collected', 0, 78036, 1);
    // animateValue('amount-required', 0, amountRequiredCount, 2000);
");

}
?>
