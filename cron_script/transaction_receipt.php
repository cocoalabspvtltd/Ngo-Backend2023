
<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

        $today=date('Y-m-d');
        
        $con=mysqli_connect("localhost","crowdworksadmin_db","Crowd212#","crowdworksadmin_db");
        $fundraiser = "select * from  fundraiser_scheme";
        $records=mysqli_query($con,$fundraiser);
         
        while($dates=mysqli_fetch_array($records)) 
        {
            $approval_status = $dates['is_approved'];
             if($approval_status == 2)
            {

                 $id = $dates['id'];
                 $to = $dates['email'];
                 $from = "noreply@crowdworksindis.org";
                 $fromName = 'CrowdWorks India Foundation';
                 $subject = "Receipt for Your Successful Fundraiser Contribution";
                 $txt =' 
                     <html> 
                     <head> 
                     <title>Welcome to CrowdWorks India Foundation</title> 
                     <style>
                    .container {
                     position: fixed;
                    top: 15%;
                    left: 25%;
                    margin-top: -90px;
                    margin-left: -100px;
                    border-radius: 7px;
                        
                    }
                    
                    .card {
                    box-sizing: content-box;
                    width: 600px;
                    height: 950px;
                    padding: 40px;
                    border: 1px solid black;
                    font-style: sans-serif;
                    background-color: #f0f0f0;
                    margin:auto;
                    margin-top:20px;
                    background-image :url("https://crowdworksindia.org/test/common/uploads/CrowdWorksIndiaReceipt/newreciept.jpg");
                     background-repeat: no-repeat;
                      background-size: cover;
                     }
    
                    .text-pdf{
                        margin-top: 40%;
                        text-align: justify;
                        line-height: 31px;
                    }
                    
                    .invoice-to
                    {
                        margin-top: 31%;
                        margin-left: 88px;
                    }
                    
                    .invoice-data
                    {
                        margin-top: 0%;
                        float: right;
                        margin-right: 91px;
                        margin-bottom : 50px;
                    }
    
                    .invoice-date
                    {
                        margin-top: -0%;
                        float: right;
                        margin-right: 0px
                       
                    }
                    
                    
                    #button {
                     background-color: #4caf50;
                     border-radius: 5px;
                     margin-left: 225px;
                    margin-bottom: 0px;
                     color: white;
                    }
                    
                    h2 {
                     text-align: center;
                     color: #24650b;
                     }
                     </style>
                    </head> 
                     <body> 
                     <h1>Thanks you for joining with us!</h1> 
                    
                    <div class="container">
                    
                    <div class="card" id="makepdf">
                    <p class="text-pdf">
                      Dear '.$dates['name'].',<br>
                      
                      We are pleased to inform you that your generous contribution to Child care on Crowd Wors India Foundation Fundraising Platform has been successfully credited.
                     </p>
                    <h4>Receipt Details</h4>
                    <ul>
                    <li>&nbsp;Donation Amount : '.$dates['fund_raised'].'</li>
                    <li>&nbsp;Fundraiser Name : '.$dates['name'].'</li>
                    <li>&nbsp;Transaction ID : dhdfth</li>
                     <li>&nbsp;Date : '.$today.'</li>
                     </ul>
                     <p>Your support is making a meanigful impact on Crowdworksindia Foundation,and we are btruly greatful for your generosity.<br>
                     If you have any questions or require further assisatance,please don`t hesitate to reach out our support team at care@crowdworksindia.org<br>
                     Once again, thank you for your support and for being a part of the positive chabge we are working towards.<br><br>
                     warm regards,<br>
                     Crowd Works India Doundation<br>
                     crowdworksindis.org<br><br><br><br>
                    </div>
                    </div>
                     </body> 
                     </html>'; 
    
                 $headers = "MIME-Version: 1.0" . "\r\n";
                 $headers .= 'From: '.$fromName.'<'.$from.'>' . "\r\n";
                 $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
    
                
                 if(mail($to,$subject,$txt,$headers)){
                    
                    $update = "UPDATE fundraiser_scheme SET is_approved ='3' WHERE id=$id";
                    $result =mysqli_query($con, $update);
                 }
                 else{
                    
                    echo "mail not send";
                 }
            } 
        }


?>
