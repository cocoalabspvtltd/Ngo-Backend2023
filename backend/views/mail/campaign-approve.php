<?php
 use yii\helpers\Url;
 $name = ucfirst($params['name']);
 $message = ucfirst($params['message']);
?>
<p style="color:#9c9c9c;font-size:16px;">
  Hi <?=$name?>,
</p>
<p style="color:#9c9c9c;font-size:16px;">
    <?=$message?>
</p>