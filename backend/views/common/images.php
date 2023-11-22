<?php
use yii\helpers\Url;
use yii\helpers\html;

$imageField= $model->image;
$documents = $model->getDocuments();
if($documents){
    foreach($documents as $key => $document){
?>
<style>
.close {
    opacity: unset;
}
</style>
<div>
<a href="<?=Url::to(['fundraiser-scheme/delete-image','id'=>$document['id']])?>"><span class="close"style="cursor: pointer;margin-right: 80%;color: red;">&times;</span></a>
<a href="<?php echo $document['url']?>" target="_blank"><img src="<?php echo $document['url']?>" alt="Click Here To View File" style="height:100px;width:100px;margin-top:10px"></a>
</div>
<?php }}?>