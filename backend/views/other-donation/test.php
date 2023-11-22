<?php
use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Agency Donation History';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<style>
    .btn-group {
    float: right !important;
    margin-top: -9px;
}

table
{
    border: 1px solid black;    
}
th
{
    border: 1px solid black;
    padding: 5px;
    background-color:skyblue;
    color: white;  
}
td
{
    border: 1px solid black;
    padding: 5px;
    color: green;
}


</style>

<?php Pjax::begin(); ?>

<?= ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'name',
    ],
    'exportConfig' => [
        ExportMenu::FORMAT_HTML => false,
        ExportMenu::FORMAT_TEXT => false,
        ExportMenu::FORMAT_PDF => false,
        ExportMenu::FORMAT_CSV => false,
        ExportMenu::FORMAT_EXCEL => [
            'label' => 'Export All',
            'filename' => 'AgencyDonations',
            'columns' => [
                'id',
                'name',
                [
                    'attribute' => 'donations',
                    'label' => 'Donations',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $donationDetails = $model['donations'];
                        $formattedDonationDetails = [];

                        foreach ($donationDetails as $donation) {
                            $formattedDonationDetails[] = [
                                'amount' => $donation['amount'],
                                'date' => $donation['date'],
                                'campaign_name' => $donation['campaign_name'],
                            ];
                        }

                        return GridView::widget([
                            'dataProvider' => new \yii\data\ArrayDataProvider([
                                'allModels' => $formattedDonationDetails,
                            ]),
                            'columns' => [
                                'campaign_name',
                                'amount',
                                'date'
                            ],
                        ]);
                    },
                ],
            ],
        ],
    ],
    'dropdownOptions' => [
        'label' => 'Export',
        'class' => 'btn btn-default',
    ],
]);

?>
 <button id="btn">Export</button> 
<?
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'name',
          [
            'attribute' => 'donations',
            'label' => 'Donations',
            'format' => 'raw',
            'value' => function ($model) {
                $donationDetails = $model['donations'];
                $formattedDonationDetails = [];

                foreach ($donationDetails as $donation) {
                    $formattedDonationDetails[] = [
                        'amount' => $donation['amount'],
                        'date' => $donation['date'],
                        'campaign_name' => $donation['campaign_name'],
                    ];
                }

                return GridView::widget([
                    'dataProvider' => new \yii\data\ArrayDataProvider([
                        'allModels' => $formattedDonationDetails,
                    ]),
                    'columns' => [
                        'campaign_name',
                        'amount',
                        'date'
                        
                    ],
                ]);
            },
        ],
      
    ],
]);

?>
 <script src= 
        "https://code.jquery.com/jquery-3.6.0.min.js"
        integrity= 
"sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" 
        crossorigin="anonymous"> 
    </script> 
<script> 
            // jQuery
$(document).ready(function () {
    $('#btn').on('click', function () {
       exportExcelReport('my-table.csv', $('#w4'));
       
    });
});

function exportExcelReport(t,d) {
    //console.log(d);
    var tab_text = "<table border='2px'><tr>";
    var table = document.getElementById(d);
    console.log(table);

    var style;
    for (var j = 0; j < table.rows.length; j++) {
        style = table.rows[j].className.split(" ");
        if (style.length < 2)
        tab_text = tab_text + table.rows[j].innerHTML + "</tr>";
    }

    tab_text = tab_text + "</table>";
    tab_text = tab_text.replace(/<a[^>]*>|<\/a>/g, "");
    tab_text = tab_text.replace(/<img[^>]*>/gi, "");
    tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, "");

    return window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
}
    </script> 