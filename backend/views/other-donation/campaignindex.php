
<style>

 #button {
    background-color: #4caf50;
    border-radius: 5px;
    color: white;
    float: right;
    margin-top: -28px;
}
</style>
 <script src=
"https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js">
 </script><h2>Campgain Donations</h2>
 <button id="button">Generate PDF</button>
<div class="panel  panel-primary" style="background-color : #337ab7;"></div>
<div class="panel-heading panel-primary" id="makepdf">
<table class="table align-middle mb-0 bg-white">
  <thead class="bg-light">
    <tr>
      <th>#</th>
      <th>Campaign</th>
      <th>Total Amount </th>
    </tr>
  </thead>
  <tbody>
      <?foreach($campaign as $item): ?>
    <tr>
      <td>
       <? echo $item->id;?>
      </td>
      <td>
       <? echo $item->title;?>
      </td>
      <td>
       <? echo $item->is_health_case; ?>
      </td>
    </tr>
    <? 
    endforeach;
    
    ?>
  </tbody>
</table>
</div>
<script>
let button = document.getElementById("button");
let makepdf = document.getElementById("makepdf");

 button.addEventListener("click", function () {
html2pdf().from(makepdf).save();
});
 </script>