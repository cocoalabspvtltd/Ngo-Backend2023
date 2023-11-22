<!DOCTYPE html>
<html>
<style>
 table, th, td {
         padding: 15px !important;
         border: 1px solid #fff !important;
         }
th
{
    background-color : #B2BEB5;
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
</style>
<body>

<h2>Payment History</h2>
  <button id="btn" class="button button2">Export</button>
<table style="width:100%" id ="empTable">
  <tr>
    <th>Agency</th>
    <th>Fundraiser Scheme</th>
    <th>Donor Name</th>
    <th>Donor Email</th>
    <th>Amount</th>
    <th>Date</th>
  </tr>
<?php
foreach($data as $key =>$items)
{
    ?>
  <tr>
    <td><?php echo $agency_name ?></td>
    <td><?php echo $fundraiser_name ?></td>
    <td><?php echo $items->name ?></td>
    <td><?php echo $items->email ?></td>
     <td><?php echo $items->amount ?></td>
    <td><?php echo $items->created_at ?></td>
   
  </tr>
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
                name: `PaymentHistory.xlsx`,
                sheet: {
                    name: 'PaymentHistory'
                }
            });
        });
    });
</script>
</html>

