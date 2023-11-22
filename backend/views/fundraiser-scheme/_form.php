<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use backend\models\Campaign;
use backend\models\RelationMaster;
use kartik\date\DatePicker;
use yii\web\View;
/* @var $this yii\web\View */
/* @var $model backend\models\FundraiserScheme */
/* @var $form yii\widgets\ActiveForm */
$campaign = Campaign::findAll(['status'=>1]);
$campaignList = ArrayHelper::map($campaign,'id','title');

$relation = RelationMaster::findAll(['status'=>1]);
$relationList = ArrayHelper::map($relation,'id','title');
?>
<style>
.close {
    opacity: unset;
}
input[type="file"] {
  display: block;
}
.imageThumb {
  max-height: 75px;
  border: 2px solid;
  padding: 1px;
  cursor: pointer;
}
.pip {
  display: inline-block;
  margin: 10px 10px 0 0;
}
.remove {
  display: block;
  background: #444;
  border: 1px solid black;
  color: white;
  text-align: center;
  cursor: pointer;
}
.remove:hover {
  background: white;
  color: black;
}
</style>
<div class="fundraiser-scheme-form">

    <?php $form = ActiveForm::begin(['method' => 'post','options' => ['enctype' => 'multipart/form-data']]); ?>
    <h3>Personal Details</h3>
    <?= $form->field($model, 'name')->textInput() ?> 

    <?= $form->field($model, 'email')->textInput() ?>

    <?= $form->field($model, 'phone_number')->textInput() ?>

    <?= $form->field($model, 'country_code')->textInput() ?>

    <?= $form->field($model, 'relation_master_id')->dropdownList($relationList,['prompt'=>'Select One........']); ?>

    <h3>Benificiary Informations</h3>
    
    <?= $form->field($model, 'title')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'fund_required')->textInput() ?>

    <?php
        if($model->id){
            $model->closing_date = date('d-m-Y',strtotime($model->closing_date));
        }
        echo $form->field($model, 'closing_date')->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Enter closing date ...'],
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'dd-mm-yyyy',
                'startDate' => 'd'
            ]
        ]); 
    ?>
    
     <?= $form->field($model, 'content_title')->textInput() ?>

    <?= $form->field($model, 'story')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'campaign_id')->dropdownList($campaignList,['prompt'=>'Select One........']); ?>

    <?= $form->field($model, 'patient_name')->textInput() ?>

    <?= $form->field($model, 'health_issue')->textInput() ?>
    
    <?= $form->field($model, 'hospital')->textInput() ?>

    <?= $form->field($model, 'city')->textInput() ?>

    <div class="row"> 
        <div class="col-md-4">
            <?= $form->field($model, 'image_url')->fileInput() ?>
        </div>
        <?php if($model->id){ ?>
            <a href="<?php echo $model->getImage()?>" target="_blank">
                <img src="<?php echo $model->getImage()?>" alt="Image" style="height:100px;width:100px;">
            </a>
        <?php }?>
    </div>
    <br>
    <br>
    <div class="row"> 
        <div class="col-md-4">
            <?= $form->field($model, 'beneficiary_image')->fileInput() ?>
        </div>
        <?php if($model->id){ ?>
            <a href="<?php echo $model->getBeneficiaryImage()?>" target="_blank">
                <img src="<?php echo $model->getBeneficiaryImage()?>" value="<?php echo $model->beneficiary_image; ?>" alt="Image" style="height:100px;width:100px;">
            </a>
        <?php }?>
    </div>
    <br>
    <br>

    <?= $form->field($model, 'documents[]')->fileInput(['multiple'=>true]) ?>
    <div class="col-md-12 margin-bottom10">
        <div class="row" id="image_preview">
        </div>
    </div>
    <?php if($model->id){ ?>
        <?php
        $documents = $model->getDocuments();
        if($documents){
            foreach($documents as $key => $document){ 
        ?>
        <div>
            <a class="remove-image" at="<?=$document['id']?>"><span class="close" style="cursor: pointer;margin-right: 80%;color: red;">&times;</span></a>
            <a href="<?php echo $document['url']?>" target="_blank"><img src="<?php echo $document['url']?>" alt="Click Here To View File" style="height:100px;width:100px;margin-top:10px"></a>
        </div>
        <?php }}?>
    <?php }?>
    
    <input type="text" name="removedFiles" id="removedFiles" value='' hidden></input>

    <h3>Benificiary Account Details</h3>

    <?= $form->field($model, 'beneficiary_account_name')->textInput() ?>

    <?= $form->field($model, 'beneficiary_account_number')->textInput() ?>

    <?= $form->field($model, 'beneficiary_ifsc')->textInput() ?>

    <?= $form->field($model, 'beneficiary_bank')->textInput() ?>

    <div class="form-group field-fundraiserscheme-beneficiary_branch has-success">
        <label class="control-label" for="fundraiserscheme-beneficiary_branch">Branch</label>
        <input type="text" id="fundraiserscheme-beneficiary_branch" class="form-control" name="beneficiary_branch" aria-invalid="false" readonly>
        <div class="help-block"></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$url = Url::to(['fundraiser-scheme/get-campaign']);
$ifscUrl = "https://bank-apis.justinclicks.com/API/V1/IFSC/";
$removeImageUrl = Url::to(['fundraiser-scheme/remove-image']);

$this->registerJs("

$(document).ready(function(){     

$('.field-fundraiserscheme-beneficiary_ifsc').change(function () {      
var inputvalues = $(this).val();      
  var reg = /[A-Z|a-z]{4}[0][a-zA-Z0-9]{6}$/;    
                if (inputvalues.match(reg)) {    
                    return true;    
                }    
                else {    
                     $('.field-fundraiserscheme-beneficiary_ifsc').val('');    
                       
                    return false;    
                }    
});


$('#fundraiserscheme-campaign_id').change(function(){
    var cam = $(this).val();
    $.ajax({  
        url: '$url',  
        type: 'POST',  
        data: {cam:cam},
        success: function(data) {  
            if(data == 0){
                $('.field-fundraiserscheme-health_issue').hide();
                $('.field-fundraiserscheme-hospital').hide();
                $('.field-fundraiserscheme-city').hide();
                $('.field-fundraiserscheme-patient_name').find('.control-label').html('Full Name');
            }else{
                $('.field-fundraiserscheme-health_issue').show();
                $('.field-fundraiserscheme-hospital').show();
                $('.field-fundraiserscheme-city').show();
                $('.field-fundraiserscheme-patient_name').find('.control-label').html('Patient Name');
            }        
        }  
    });  
});
$('.remove-image').click(function(){
    var id = $(this).attr('at');
    $.ajax({  
        url: '$removeImageUrl',  
        type: 'POST',  
        data: {id:id},
        success: function(data) {  
            if(data){
                if(alert('Document Deleted successfully')){}else location.reload(); 
            }   
        }  
    });
});
$('#fundraiserscheme-beneficiary_ifsc').on('input',function(){
    var code = $(this).val();
    $('#fundraiserscheme-beneficiary_bank').val('');
    $('#fundraiserscheme-beneficiary_branch').val('');
    $.get('$ifscUrl'+code, function(data){
        if(data){
            var values = JSON.parse(data);
            $('#fundraiserscheme-beneficiary_bank').val(values.BANK);
            $('#fundraiserscheme-beneficiary_branch').val(values.BRANCH);
        }
    });
});





var newFileList = null;
var selectedVal = null;
var selectedId = [];

$('#fundraiserscheme-documents').change(function(){
    var total_file=$('#fundraiserscheme-documents')['0'].files.length;
    newFileList = Array.from(event.target.files);
    selectedVal = Array.from(event.target.files);
    for(var i=0;i<total_file;i++){
        $('#image_preview').append('<div class=\"col-md-2 margin-top10 appendedImg\"><img alt=\"It is a Doc File\" class=\"imageThumb\" src=\"'+URL.createObjectURL(event.target.files[i])+'\"><button class=\"remove btnRemove\" value=\"'+i+'\">Remove</button></div>');
    }
});

});

"); 

?>  