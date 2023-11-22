
<!DOCTYPE html>
<html lang="en">
<head>
<!-- html2pdf CDN-->
 <script src=
"https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js">
 </script>

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
height: 750px;
padding: 40px;
border: 1px solid black;
font-style: sans-serif;
background-color: #f0f0f0;
margin:auto;
margin-top:20px;
background-image :url('/ngo/common/uploads/CrowdWorksIndiaReceipt/newreciept.jpg');
 background-repeat: no-repeat;
  background-size: cover;
 }

.text-pdf{
    margin-top:0%;
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
    margin-top: -5%;
    float: right;
    /* margin-left: 42px; */
    margin-right: 70px;
}

.invoice-date
{
    margin-top: -0%;
    float: right;
    /* margin-left: 42px; */
    margin-right: 50px
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
<div class="container">
<button id="button">Generate PDF</button>
<div class="card" id="makepdf">
<p class="invoice-to"><b><?php echo $form_data->name;?></b></p>
<p class="invoice-data"><b>Invoice.<?php echo $a?></b></p>
<p class="invoice-date"><?php echo $date; ?></p><br><br>
<p class="text-pdf">
With sincere appreciation and heartfelt gratitude, we write to express our deepest thanks for your recent donation of <u><b>Rs.<?php echo $form_data->amount;?></b></u> to Crowd Works India Foundation. Your incredible act of kindness has touched our hearts and will have a lasting impact on our mission.
We are truly humbled by your trust and belief in our work. Your donation not only provides financial support but also serves as a testament to the power of compassion and unity.Once again, we extend our deepest gratitude for your remarkable generosity. If you have any further questions or would like to stay updated on our progress, please do not hesitate to contact us. We would be honored to connect with you and provide any information you may need.
<br>With utmost appreciation
 </p>
</div>
</div>

<script>
let button = document.getElementById("button");
let makepdf = document.getElementById("makepdf");

 button.addEventListener("click", function () {
html2pdf().from(makepdf).save();
});
 </script>
</body>
</html>
