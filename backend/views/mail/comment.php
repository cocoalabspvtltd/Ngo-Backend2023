<?php
 use yii\helpers\Url;
 $name = ucfirst($params['name']);
 $model = $params['model'];
 $message = ucfirst($params['message']);
?>
<p style="color:#9c9c9c;font-size:16px;">
  Hi <?=$name?>,
</p>
<p style="color:#9c9c9c;font-size:16px;">
    Admin commented on your Fundraiser Scheme <?=$model->title?>,
</p>
<p>
    <?=$message?>
</p>
