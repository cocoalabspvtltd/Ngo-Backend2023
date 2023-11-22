<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\FundraiserScheme */

$this->title = 'Update Campaign: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Campaigns List', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fundraiser-scheme-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
<?php

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

});

"); 
?> 
