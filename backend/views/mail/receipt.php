<?php
 use yii\helpers\Url;
 $name = ucfirst($params['name']);
 $title = $params['title'];
 $message = ucfirst($params['message']);
 $amount = ($params['amount']);
?>
<p style="color:#9c9c9c;font-size:16px;">
  Hi <?=$name?>,
</p>
<p style="color:#9c9c9c;font-size:16px;">
    Thank you for your great generosity! We at Crowd Works India Foundation, 
    greatly appreciate your donation of RS <?=$amount?> for <?=$title?>, 
    and your sacrifice.
</p>
