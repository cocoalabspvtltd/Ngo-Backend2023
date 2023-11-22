<?php
 $to = "meghanison20@gmail.com";
 $subject = "My subject";
 $txt = "test one!";
 $headers = "From: prezenty.designs@gmail.com" . "\r\n" ;

 if(mail($to,$subject,$txt,$headers)){
    
     echo "mail send";
 }
 else{
    
    echo "mail not send";
 }
 ?>