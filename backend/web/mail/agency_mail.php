
<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Yii;
    
  //require("PHPMailer/src/PHPMailer.php");
 // require("PHPMailer/src/Exception.php");
 // require("PHPMailer/src/SMTP.php");


                     
    $mail = new PHPMailer();
    $mail->isMail(); // enable SMTP

    $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 535; // or 587
    $mail->IsHTML(true);
    $mail->Username = "pgsedu20@gmail.com";
    $mail->Password = "EWdupro@123";
    $mail->SetFrom("pgsedu20@gmail.com");
    $mail->Subject = "Test";
    $mail->Body = "Your Payment Link is ";
    $mail->AddAddress("nimakcpy98@gmail.com");

     if(!$mail->Send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
     } else {?>
      <script language="javascript" type="text/javascript">
    alert('Send user Credential successfully.');
    window.location.href = 'https://www.cocoalabs.in/ngo/backend/web/agency-landing-page/index';
                 </script>   <?php              
     }
?>

