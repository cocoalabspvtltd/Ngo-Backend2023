<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<!DOCTYPE html>
<html>
   <head>
      <style>
         table, th, td {
         padding: 15px !important;
         border: 1px solid #fff !important;
         }
         .button {
              background-color: #3c8dbc; /* Green */
              border: none;
              color: white;
              padding: 15px 32px;
              text-align: center;
              text-decoration: none;
              display: inline-block;
              font-size: 16px;
              margin: 4px 2px;
              cursor: pointer;
              -webkit-transition-duration: 0.4s; /* Safari */
              transition-duration: 0.4s;
              float : right;
            }

        .button2:hover {
         box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24),0 17px 50px 0 rgba(0,0,0,0.19);
        }
        tr {
             padding: 10px !important;
        }
      </style>
   </head>
   <body>
      
      <h2>Agency Donation History</h2>
       <button id="btn" class="button button2">Export</button>
    <table width="100%" class="kv-grid-table table table-bordered kv-table-wrap" id ="empTable">
     <tr style="text-align:center;background-color: skyblue;
    color: white;">
    <td>#</td>     
  <td>Agency</td>
  <th>Campagin name</th><th>Amount</th><th>Date</th>
 </tr>
    <?php
    foreach ($combinedData as $agencyData) {
        $name = $agencyData['name'];
        $donationDetails = $agencyData['donations'];
        $donationDetailsLength = count($donationDetails);
        ?>

        <tr>
            <td rowspan="<?php echo $donationDetailsLength; ?>">#</td>
            <td rowspan="<?php echo $donationDetailsLength; ?>"><?php echo $name; ?>
            </td>

            <?php
            if ($donationDetailsLength > 0) {
                $donation = $donationDetails[0];
                $campaignName = $donation['campaign_name'];
                $amount = $donation['amount'];
                $date = $donation['date'];
                $dateTime = new DateTime($date);
                $formattedDate = $dateTime->format('d/m/Y');
                ?>
                <td><?php echo $campaignName; ?></td>
                <td><?php echo $amount; ?></td>
                <td><?php echo $formattedDate; ?></td>
            <?php
            } else {
                // Handle the case where there are no donations for the agency.
                echo '<td colspan="3">No donations</td>';
            }
            ?>

        </tr>

        <?php
        // If there are multiple donation rows, loop through the rest.
        for ($i = 1; $i < $donationDetailsLength; $i++) {
            $donation = $donationDetails[$i];
            $campaignName = $donation['campaign_name'];
            $amount = $donation['amount'];
            $date = $donation['date'];
            $dateTime = new DateTime($date);
            $formattedDate = $dateTime->format('d/m/Y');
            ?>
            <tr style="background-color: #ffffff !important;">
                <td><?php echo $campaignName; ?></td>
                <td><?php echo $amount; ?></td>
                <td><?php echo $formattedDate; ?></td>
            </tr>
        <?php
        }
        ?>
    <?php
    }
    ?>
</table>

   </body>
   <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>
<script>
    $(document).ready(function () {
        $("#btn").click(function () {
            let table = document.getElementsByTagName("table");
            console.log(table);
            debugger;
            TableToExcel.convert(table[0], {
                name: `UserManagement.xlsx`,
                sheet: {
                    name: 'Usermanagement'
                }
            });
        });
    });
</script>
</html>
