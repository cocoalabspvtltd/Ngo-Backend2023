<?php

namespace backend\controllers;

use Yii;
use backend\models\Donation;
use backend\models\Agency;
use backend\models\Campaign;
use backend\models\FundraiserScheme;
use backend\models\DonationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use kartik\export\ExportMenu;

/**
 * DonationController implements the CRUD actions for Donation model.
 */
class OtherDonationController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST','GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','create','update','view','delete'],
                'rules' => [
                    [
                        'actions' => ['index','create','update','delete','view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function($rule, $action) {
                return $this->goHome();
                }
                ]
        ];
    }

    /**
     * Lists all Donation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DonationSearch();
        $dataProvider = $searchModel->searchOthers(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionCampaignindex()
    {
       
    $campaign =Campaign::find()
            ->select(['campaign.title','fundraiser_scheme.campaign_id','fundraiser_scheme.id']) 
            ->leftJoin('fundraiser_scheme','fundraiser_scheme.campaign_id = campaign.id')
            ->where(['campaign.status'=> 1])
            ->all();
            
            $uniqueCampaigns = [];
   
            foreach($campaign as $key=>$items){
                
            $uniqueCampaigns[$items->title] = $items;
             
             $sum = Donation::find()
             ->where(['fundraiser_id' => $items->id])
            ->sum('amount');
            
              $campaign[$key]->is_health_case = $sum;
          }

         // $uniqueCampaignArray = array_values($uniqueCampaigns);
         // print_r($uniqueCampaignArray);
            
        return $this->render('campaignindex', [
            'campaign' => $campaign,
        ]);
    }
    
    public function actionAgencyindex()
    {
        
     $agencies = Agency::find()->asArray()->all();

     
        $combinedData = [];

       
        foreach ($agencies as $agency) {
           
            $agencyDetails = [
                'id' => $agency['id'],
                'name' => $agency['name'],
            ];
            
            // Fetch donations for the agency.
            $donations = Donation::find()
                ->where(['agency_id' => $agency['id']])
                ->asArray()
                ->all();

            // Iterate through donations for the agency.
            $donationDetails = [];
            foreach ($donations as $donation) {
                // Fetch campaign details based on campaign_id.
                $campaign = Campaign::find()
                    ->where(['id' => $donation['campaign_id']])
                    ->asArray()
                    ->one();

                if ($campaign) {
                    // Combine the donation and campaign details.
                    $donationDetails[] = [
                        'amount' => $donation['amount'],
                        'date' => $donation['created_at'],
                        'campaign_name' => $campaign['title'],
                    ];
                }
            }

            $agencyDetails['donations'] = $donationDetails;

            $combinedData[] = $agencyDetails;
           // print_r($combinedData);exit;
            
        }

          return $this->render('agencydonationindex', [
            'combinedData' => $combinedData
        ]);
    }
    
     public function actionAgencyDonationHistory()
    {
        $agencies = Agency::find()->asArray()->all();

     
        $combinedData = [];

       
        foreach ($agencies as $agency) {
           
            $agencyDetails = [
                'id' => $agency['id'],
                'name' => $agency['name'],
            ];

            // Fetch donations for the agency.
            $donations = Donation::find()
                ->where(['agency_id' => $agency['id']])
                ->asArray()
                ->all();

            // Iterate through donations for the agency.
            $donationDetails = [];
            foreach ($donations as $donation) {
                // Fetch campaign details based on campaign_id.
                $campaign = Campaign::find()
                    ->where(['id' => $donation['campaign_id']])
                    ->asArray()
                    ->one();

                if ($campaign) {
                    // Combine the donation and campaign details.
                    $donationDetails[] = [
                        'amount' => $donation['amount'],
                        'date' => $donation['created_at'],
                        'campaign_name' => $campaign['title'],
                    ];
                }
            }

            $agencyDetails['donations'] = $donationDetails;

            $combinedData[] = $agencyDetails;
        }

        // Create an ArrayDataProvider with combined data.
        $dataProvider = new ArrayDataProvider([
            'allModels' => $combinedData,
        ]);

        
    return $this->render('test', [
        'dataProvider' => $dataProvider,
    ]);
    
    }
    
    public function actionExport()
    {
        // Create a data provider with your agencies and donation details data
        $agencies = Agency::find()->asArray()->all();

     
        $combinedData = [];

       
        foreach ($agencies as $agency) {
           
            $agencyDetails = [
                'id' => $agency['id'],
                'name' => $agency['name'],
            ];

            // Fetch donations for the agency.
            $donations = Donation::find()
                ->where(['agency_id' => $agency['id']])
                ->asArray()
                ->all();

            // Iterate through donations for the agency.
            $donationDetails = [];
            foreach ($donations as $donation) {
                // Fetch campaign details based on campaign_id.
                $campaign = Campaign::find()
                    ->where(['id' => $donation['campaign_id']])
                    ->asArray()
                    ->one();

                if ($campaign) {
                    // Combine the donation and campaign details.
                    $donationDetails[] = [
                        'amount' => $donation['amount'],
                        'date' => $donation['created_at'],
                        'campaign_name' => $campaign['title'],
                    ];
                }
            }

            $agencyDetails['donations'] = $donationDetails;

            $combinedData[] = $agencyDetails;
        }

        // Create an ArrayDataProvider with combined data.
        $dataProvider = new ArrayDataProvider([
            'allModels' => $combinedData,
        ]);
        // Define columns for the export
        $columns = [
            'id',
            'name',
            [
                'attribute' => 'donations',
                'label' => 'Donations',
                'format' => 'raw',
                'value' => function ($model) {
                    // Generate and return the nested GridView for donation details
                    return GridView::widget([
                        'dataProvider' => new \yii\data\ArrayDataProvider([
                            'allModels' => $model['donations'],
                        ]),
                        'columns' => [
                            'campaign_name',
                            'amount',
                            'date',
                        ],
                    ]);
                },
            ],
        ];

        // Use the ExportMenu widget to create the export view
        echo ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $columns,
            'fontAwesome' => true,
            'filename' => 'exported-data',
            'exportConfig' => [
                ExportMenu::FORMAT_HTML,
                ExportMenu::FORMAT_CSV,
                ExportMenu::FORMAT_EXCEL,
                ExportMenu::FORMAT_PDF,
            ],
        ]);
    }
    
        
    protected function findModel($id)
    {
        if (($model = Donation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    

}
