<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\web\UploadedFile;
use backend\models\User;
use backend\models\Visitor;
use backend\models\FundraiserScheme;
use backend\models\Donation;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index','profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $totalVisitors = Visitor::find()->where(['status'=>1])->count();
        $model = new FundraiserScheme;
        $get = Yii::$app->request->get();
        if($get && $model->load($get)){
            $totalFundraisersQry = FundraiserScheme::find()
            ->leftJoin('user','user.id=fundraiser_scheme.created_by')
            ->where(['fundraiser_scheme.status'=>1])
            ->andWhere(['user.role'=>'campaigner']);
            if($get['FundraiserScheme']['from_date']){
                $totalFundraisersQry->andFilterWhere(['>=','DATE(fundraiser_scheme.created_at)',date('Y-m-d',strtotime($get['FundraiserScheme']['from_date']))]); 
            }
            if($get['FundraiserScheme']['to_date']){
                $totalFundraisersQry->andFilterWhere(['<=','DATE(fundraiser_scheme.created_at)',date('Y-m-d',strtotime($get['FundraiserScheme']['to_date']))]); 
            }
            $totalFundraisers = $totalFundraisersQry->count();
            $totalUsersQry = User::find()->where(['status'=>1,'role'=>'campaigner']);
            if($get['FundraiserScheme']['from_date']){
                $totalUsersQry->andFilterWhere(['>=','DATE(created_at)',date('Y-m-d',strtotime($get['FundraiserScheme']['from_date']))]); 
            }
            if($get['FundraiserScheme']['to_date']){
                $totalUsersQry->andFilterWhere(['<=','DATE(created_at)',date('Y-m-d',strtotime($get['FundraiserScheme']['to_date']))]); 
            }
            $totalUsers = $totalUsersQry->count();
            $totalCampaignsQry = FundraiserScheme::find()
            ->leftJoin('user','user.id=fundraiser_scheme.created_by')
            ->where(['fundraiser_scheme.status'=>1])
            ->andWhere(['user.role'=>'super-admin']);
            if($get['FundraiserScheme']['from_date']){
                $totalCampaignsQry->andFilterWhere(['>=','DATE(fundraiser_scheme.created_at)',date('Y-m-d',strtotime($get['FundraiserScheme']['from_date']))]); 
            }
            if($get['FundraiserScheme']['to_date']){
                $totalCampaignsQry->andFilterWhere(['<=','DATE(fundraiser_scheme.created_at)',date('Y-m-d',strtotime($get['FundraiserScheme']['to_date']))]); 
            }
            $totalCampaigns = $totalCampaignsQry->count();
            $totalFundRequiredQry = FundraiserScheme::find()
            ->leftJoin('user','user.id=fundraiser_scheme.created_by')
            ->where(['fundraiser_scheme.status'=>1]);
            if($get['FundraiserScheme']['from_date']){
                $totalFundRequiredQry->andFilterWhere(['>=','DATE(fundraiser_scheme.created_at)',date('Y-m-d',strtotime($get['FundraiserScheme']['from_date']))]); 
            }
            if($get['FundraiserScheme']['to_date']){
                $totalFundRequiredQry->andFilterWhere(['<=','DATE(fundraiser_scheme.created_at)',date('Y-m-d',strtotime($get['FundraiserScheme']['to_date']))]); 
            }
            $totalFundRequired = $totalFundRequiredQry->sum('fund_required');
            $totalSupporters = Donation::find()->where(['status'=>1])->count();
            $totalFundRaisedQry = Donation::find()->where(['status'=>1]);
            if($get['FundraiserScheme']['from_date']){
                $totalFundRaisedQry->andFilterWhere(['>=','DATE(created_at)',date('Y-m-d',strtotime($get['FundraiserScheme']['from_date']))]); 
            }
            if($get['FundraiserScheme']['to_date']){
                $totalFundRaisedQry->andFilterWhere(['<=','DATE(created_at)',date('Y-m-d',strtotime($get['FundraiserScheme']['to_date']))]); 
            }
            $totalFundRaised = $totalFundRaisedQry->sum('amount');
        }else{
            $totalUsers = User::find()->where(['status'=>1,'role'=>'campaigner'])->count();
            $totalFundraisers = FundraiserScheme::find()
            ->leftJoin('user','user.id=fundraiser_scheme.created_by')
            ->where(['fundraiser_scheme.status'=>1])
            ->andWhere(['user.role'=>'campaigner'])->count();
            $totalCampaigns = FundraiserScheme::find()
            ->leftJoin('user','user.id=fundraiser_scheme.created_by')
            ->where(['fundraiser_scheme.status'=>1])
            ->andWhere(['user.role'=>'super-admin'])->count();
            $totalFundRequired = FundraiserScheme::find()
            ->leftJoin('user','user.id=fundraiser_scheme.created_by')
            ->where(['fundraiser_scheme.status'=>1])->sum('fund_required');
            $totalSupporters = Donation::find()->where(['status'=>1])->count();
            $totalFundRaised = Donation::find()->where(['status'=>1])->sum('amount');
        }
        return $this->render('index',[
            'totalVisitors' => $totalVisitors,
            'totalUsers' => $totalUsers,
            'totalFundraisers' => $totalFundraisers,
            'totalCampaigns' => $totalCampaigns,
            'totalFundRaised' => $totalFundRaised,
            'totalFundRequired' => $totalFundRequired,
            'model' => $model
        ]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    
    }
    

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionProfile(){
        $model = User::findOne(Yii::$app->user->identity->id);
        $image = $model->image_url;
        $post = Yii::$app->request->post();
        if($post){
            if($model->load($post) && $model->validate()){

                $imageUrl = UploadedFile::getInstances($model,'image_url');
                if($imageUrl && !empty($imageUrl)){
                    $imageLocation = Yii::$app->params['upload_path_profile_images'];
                    $modelUser = new User;
                    $saveImage = $modelUser->uploadAndSave($imageUrl,$imageLocation);
                    if($saveImage){
                        $model->image_url = $saveImage;
                    }
                }else{
                    $model->image_url = $image;
                } 

                if($model->new_password){
                    $model->password_hash = Yii::$app->getSecurity()->generatePasswordHash($model->new_password);
                }
                $model->save(false);
                yii::$app->session->setFlash('success','Profile Updated Successfully');
                return $this->redirect(['index']);
            }
        }
        return $this->render('profile',[
            'model' => $model
        ]);
    }
}
