<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

        $today=date('Y-m-d');
        
        $con=mysqli_connect("localhost","crowdworksadmin_db","Crowd212#","crowdworksadmin_db");
        $fundraiser = "select * from  fundraiser_scheme where id= 345";
        $records=mysqli_query($con,$fundraiser);
        
        return $fundraiser;
?>