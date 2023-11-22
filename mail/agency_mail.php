     
    <?php
    
        ini_set('display_errors',1);
         error_reporting(E_ALL);
         
       $agency_page_id = $_GET['id'];
       $con=mysqli_connect("localhost","cocoaejr","UXQlHcAu}^Cn","cocoaejr_ngo");
       $agency_page="select * from agency_landing_page where id=$agency_page_id";
       $records=mysqli_query($con,$agency_page);
        while($row=mysqli_fetch_array($records))
		{
		    $agency_details="select * from agency where id=".$row['agency_id'];
            $results=mysqli_query($con,$agency_details); 
            
            while($rows=mysqli_fetch_array($results))
		    {
		        $email = $rows['email'];
		        $landing_page_url = $row['landing_page_url'];
                $from ="info@crowdworksindia.org";
                $to =  $email;
                $subject = "Payment Url";
                $message ="<html>
                   <body>
                   <h3>Dear</h3>
                   <h3>Your Payment link for fund raise</h3>
                       <p>$landing_page_url</p>
                   <h3>Click on this link and Donate</h3></body></html>";
               $headers = "From:".$from;
               $headers.= "MIME-Version: 1.0\r\n";
               $headers.= "Content-type: text/html;charset-ISO:8859-1\r\n";
         
               if(mail($to,$subject,$message,$headers)){?>
                   <script language="javascript" type="text/javascript">
                    alert('Send user Credential successfully.');
                    window.location.href = 'https://www.cocoalabs.in/ngo/backend/web/agency-landing-page/index';
                  </script>
                  <?php
                    
                } 
         
		   }
         
		}  
		
		
           
    ?>        