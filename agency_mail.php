<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Yii;
    
  require("vendor/PHPMailer/src/PHPMailer.php");
  require("vendor/PHPMailer/src/Exception.php");
  require("vendor/PHPMailer/src/SMTP.php");
  
  
   $page_id = 32;//$_GET['id'];
   $con=mysqli_connect("localhost","cocoaejr_cocoaej","iLoveCoco007","cocoaejr_ngo");
   $agency_details="SELECT  `agency_landing_page`.*, `agency`.`*` FROM    `agency_landing_page`
                LEFT JOIN `agency` ON `agency`.`id` = `agency_landing_page`.`agency_id`
                AND `agency_landing_page`.`id` = '$page_id'  ";
   $records=mysqli_query($con,$agency_details);


      while($row=mysqli_fetch_array($records))    
      {
          
         $to = $row['email'];
         $subject = "Agency Donation Link";
         
         $message = "Your Payment Link is ".$row['landing_page_url'];
         
         $header = "From:pgsedu20@gmail.com\r\n";
         $header .= "Cc:cocolabslekshmi@gmail.com \r\n";
         $header .= "MIME-Version: Agency Link\r\n";
         $header .= "Content-type: text/html\r\n";
         
         $retval = mail ($to,$subject,$message,$header);
         
         if( $retval == true ) {?>
            <script language="javascript" type="text/javascript">
             alert('Send agency link successfully.');
             window.location.href = 'https://www.cocoalabs.in/ngo/backend/web/agency-landing-page/index';
             </script><?php
         }else {
              echo "Message could not be sent...";
         }
      }
?>


    
