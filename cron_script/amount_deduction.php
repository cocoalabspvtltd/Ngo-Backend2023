 <?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

        $today=date('Y-m-d');
        $current_day= date("d", strtotime($today));
        global $deducted_amount;
        
        $con=mysqli_connect("localhost","crowdworksadmin_db","Crowd212#","crowdworksadmin_db");
        $fundraiser = "select * from  fundraiser_scheme where created_by != 1";
        $records=mysqli_query($con,$fundraiser);
     
        
        while($dates=mysqli_fetch_array($records)) {
          
            
            $closing=$dates['closing_date'];
            $fund_required=$dates['fund_required'];
            $goal_amount=$dates['fund_raised'];
            $fundraiser_id=$dates['id'];
            $user_id = $dates['created_by'];
           
            $payment_charge = "SELECT SUM(payment_charge) FROM transaction WHERE fundraiser_id= $fundraiser_id";
             $charge_records=mysqli_query($con,$payment_charge);
             
             while($charges=mysqli_fetch_array($charge_records))
             {
                 
               if($today==$closing){
               
               $transaction_id=$dates['id'];
               $pricing_id=$dates['pricing_id'];
               
              if($pricing_id==1){
        
                  $pricing=0;
                  $deducted_amount=($pricing / 100) * $goal_amount;
                  $deduct_charge = ($charges[0] / 100) * $deducted_amount;
                  $deducted = $deducted_amount + $deduct_charge;
                  $balance=$goal_amount-$deducted;

                }else if($pricing_id==2){
                    
                  $pricing=5;
                  $deducted_amount=($pricing / 100) * $goal_amount;
                  $deduct_charge = ($charges[0] / 100) * $deducted_amount;
                  $deducted = $deducted_amount + $deduct_charge;
                  $balance=$goal_amount-$deducted;

                }else if($pricing_id==3){

                  $pricing=8;
                  $deducted_amount=($pricing / 100) * $goal_amount;
                  $deduct_charge = ($charges[0] / 100) * $deducted_amount;
                  $deducted = $deducted_amount + $deduct_charge;
                  $balance= $goal_amount-$deducted;
                }

             $transfer_query="INSERT INTO transfer_request (user_id, fundraiser_id, transferred_amount,amount,deducted_amount)VALUES ($user_id, $fundraiser_id, $balance,$goal_amount,$deducted)";
             
              $query= "UPDATE transaction SET deducted_amount =$deducted,amount=$balance,tag='deducted'  WHERE fundraiser_id=$fundraiser_id";
              
              $update_query = "UPDATE deducted_amount SET deducted_amount =$deducted,amount=$balance   WHERE fundraiser_id=$fundraiser_id";
              
              $result=mysqli_query($con,$query);
              $transfer_result=mysqli_query($con,$transfer_query);
              $results= mysqli_query($con,$update_query);
              
            }
            elseif ($fund_required==$goal_amount){
                
              $transaction_id=$dates['id'];
              $pricing_id=$dates['pricing_id'];

              if($pricing_id==1){
        
                  $pricing=0;
                  $deducted_amount=($pricing / 100) * $goal_amount;
                  $deduct_charge = ($charges[0] / 100) * $deducted_amount;
                  $deducted = $deducted_amount + $deduct_charge;
                  $balance=$goal_amount-$deducted;

                }else if($pricing_id==2){
        
                  $pricing=5;
                  $deducted_amount=($pricing / 100) * $goal_amount;
                  $deduct_charge = ($charges[0] / 100) * $deducted_amount;
                  $deducted = $deducted_amount + $deduct_charge;
                  $balance=$goal_amount-$deducted;

                }else if($pricing_id==3){

                  $pricing=8;
                  $deducted_amount=($pricing / 100) * $goal_amount;
                  $deduct_charge = ($charges[0] / 100) * $deducted_amount;
                  $deducted = $deducted_amount + $deduct_charge;
                  $balance=$goal_amount-$deducted;
                }
                
                $transfer_query="INSERT INTO transfer_request (user_id, fundraiser_id, transferred_amount,amount,deducted_amount)VALUES ($user_id, $fundraiser_id, $balance,$goal_amount,$deducted)";

              $query= "UPDATE transaction SET deducted_amount =$deducted,tag='deducted',amount=$balance WHERE fundraiser_id=$fundraiser_id";
              
              $update_query = "UPDATE deducted_amount SET deducted_amount =$deducted,amount=$balance  WHERE fundraiser_id=$fundraiser_id";
              
              $result=mysqli_query($con,$query);
              $transfer_result=mysqli_query($con,$transfer_query);
              $results= mysqli_query($con,$update_query);
              

            }    
            
            }        
            
    
      }
    
