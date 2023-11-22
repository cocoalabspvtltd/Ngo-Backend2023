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
 top: 16%;
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
 margin:auto;
 margin-top:20px;
 background-color: #f0f0f0;
 background-image :url('/ngo/common/uploads/80GForm/80g Crowd Works.jpg');
 background-repeat: no-repeat;
 background-size: cover;
 margin-left:10%;
 }

 .text-pdf{
     
     margin-top:23%;
     font-size: 19px;
     
 }
 #button {
 background-color: #4caf50;
 border-radius: 5px;
 margin-left: 116px;
 margin-bottom: 2px;
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
<p class="text-pdf">
<h5>Date: <b><?php echo $date;?></b></h5>
<h5>Certificate No: <b><?php echo $certificate_number;?></b></h5>
<h5>Dear,</h5>
<p class="text-size">This is to Confirm that Crowd Works India Foundation recived a total amount of Rs.<u><b><?php echo $form_data->amount;?></b></u> from <u><b><?php $form_data->name; ?></b></u> PAN no. <u><b><?php echo $form_data->pan_number;?></b></u> as per the details given </p><br>
<h5>Date: <b><?php echo $date;?></b></h3>
<h5>Amount: <b><?php echo $form_data->amount;?></b></h3>
<h5>Invoice no: <b>AVd32425</b></h3><br>
<p class="text-size">Heartfelt gratitude for your generous donation. Your support will go a long way in helping us achieve our mission.</p><br>
<p class="text-size">This is a computer-generated receipt and does not require a sign. <b>Crowd Works India Foundation</b> PAN no. : <b><?php echo $form_data->pan_number;?></b></p>
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