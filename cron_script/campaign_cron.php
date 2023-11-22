<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

        $today=date('Y-m-d');
        $con=mysqli_connect("localhost","crowdworksadmin_db","Crowd212#","crowdworksadmin_db");
        $fundraiser = "select * from  fundraiser_scheme";
        $records=mysqli_query($con,$fundraiser);
         
        while($dates=mysqli_fetch_array($records)) 
        {
          
            $closing=$dates['closing_date'];
            $removal_date = date('Y-m-d', strtotime($closing . ' +1 day'));
            $fund_required=$dates['fund_required'];
            $goal_amount=$dates['fund_raised'];
            $approved_status= $dates['is_approved'];
            if($removal_date == $today)
            {
                if($approved_status != 3)
                {
                   $campaign_id=$dates['id'];
                   $remove_query = "UPDATE fundraiser_scheme SET is_approved ='4' WHERE id=$campaign_id";
                   $remove_result =mysqli_query($con, $remove_query);
                }
            }
            else if($fund_required == $goal_amount)
            {
                if($approved_status != 3)
                {
                   $campaign_id=$dates['id'];
                   $remove_query = "UPDATE fundraiser_scheme SET is_approved ='4' WHERE id=$campaign_id";
                   $remove_result =mysqli_query($con, $remove_query);
                }
            }
            
        }
?>