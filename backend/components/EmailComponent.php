<?php
namespace backend\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use frontend\models\TbMessage;
use frontend\models\TbPackage;
use backend\models\Person;
use backend\models\User;
use backend\models\Supplier;
use backend\models\Account;
use backend\models\DepartmentHead;
use backend\models\Bdm;
use backend\models\FundraiserScheme;
use backend\models\Agency;
use jobs\models\TbFile;
class EmailComponent extends Component
{
  	public $SENDGRID_API_KEY;
  	function __construct($config=[]) {
      	parent::__construct($config);
  	}


  	public function getList($name) {
    	$url = "https://api.sendgrid.com/v3/contactdb/lists";
    	$sendgrid = Yii::$app->sendGrid;
    	$response = $this->curlGet($url,$headers);
  	}


  	public function addList($name) {
    	$url = 'https://api.sendgrid.com/v3/contactdb/lists';
    	$sendgrid = Yii::$app->sendGrid;
    	$data = [
      		'name' => $name
    	];
    	$data = json_encode($data);
    	$token = $this->SENDGRID_API_KEY;
    	$headers = [
      		"Authorization: Bearer $token"
    	];
    	$response = $this->curlPost($url,$data,$headers);
    	$response = json_decode($response);
    	return $response;
  	}


  	public function addSubscriber($email) {
    	$listName = isset(Yii::$app->params['subscriber-list-name'])?Yii::$app->params['subscriber-list-name']:'marketing';
    	$this->addList($listName);
    	$url = "https://api.sendgrid.com/v3/contactdb/recipients";
    	$sendgrid = Yii::$app->sendGrid;
    	$token = $this->SENDGRID_API_KEY;
    	$data = [
      		[
        		'email' => $email
      		]
    	];
    	$data =  json_encode($data);
    	$headers = [
      		"Authorization: Bearer $token"
    	];
    	$response = $this->curlPost($url,$data,$headers);
    	$params = [];
    	$response = json_decode($response);
    	return $response;
  	}


  	public function curlPost($url, $data=NULL, $headers = NULL) {
    	$ch = curl_init($url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	if(!empty($data)){
        	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    	}
    	if (!empty($headers)) {
        	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	}
    	$response = curl_exec($ch);
    	if (curl_error($ch)) {
        	trigger_error('Curl Error:' . curl_error($ch));
    	}
    	curl_close($ch);
    	return $response;
	}


  	public function curlGet($url, $headers = NULL) {
    	$ch = curl_init($url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	if (!empty($headers)) {
        	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	}
    	$response = curl_exec($ch);
    	if (curl_error($ch)) {
        	trigger_error('Curl Error:' . curl_error($ch));
    	}
    	curl_close($ch);
    	return $response;
	}


	public function sendEmail($modelMemo) {
		$email = $modelMemo->email;
		$name = $modelMemo->name;
		$amount = $modelMemo->amount;
		$description = $modelMemo->description;
		$memoId = $modelMemo->id;
		$to = [
			'name' => $name,
			'email' => $email
		];
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
 		$params = [
			'memoId' => $memoId,
			'amount' => $amount,
			'name'=>$name,
			'description' => $description
  		];
 		$view = 'memo-send-mail';
 		$subject = $modelMemo->subject;
 		$message = '';
  		$this->sendMail($from,$to,$subject,$message,$view,$params);
	}


	public function sendPaidEmail($modelMemo) {
		$email = $modelMemo->email;
		$name = $modelMemo->name;
		$amount = $modelMemo->amount;
		$description = $modelMemo->description;
		$to = [
			'name' => $name,
			'email' => $email
		];
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$params = [
			'amount' => $amount,
			'name'=>$name,
			'description' => $description
		];
 		$view = 'memo-paid-send-mail';
		$subject = $modelMemo->subject;
		$message = '';
  		$this->sendMail($from,$to,$subject,$message,$view,$params);
	}

	
	public function sendPaidInvoice($modelMemo,$modelLsgi) {
		$email = $modelMemo->email;
		$name = $modelMemo->name;
		$amount = $modelMemo->amount;
		$description = $modelMemo->description;
		if($modelLsgi){
			$modelLogoImage        = $modelMemo->getImage($modelLsgi->image_id);
			if(isset($modelLogoImage)?$modelLogoImage:''){
				$url = $modelLogoImage->uri_full;
				$path =  Yii::$app->params['logo_image_base_url'];
				$logoUrl = $modelLogoImage->getFullUrl($url,$path);
			}
		}
		if($modelLsgi)
			$lsgiAddress = $modelLsgi->address;
		$to = [
			'name' => $name,
			'email' => $email
		];
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$params = [
			'amount' => $amount,
			'name'=>$name,
			'description' => $description,
			'logoUrl' => $logoUrl,
			'lsgiAddress' => $lsgiAddress
		];
		$view = 'invoice';
		$subject = $modelMemo->subject;
		$message = '';
		$this->sendMail($from,$to,$subject,$message,$view,$params);
	}


  	public function sendPasswordReset($modelUser,$email) {
    	$firstName = '';
    	$modelPerson = Supplier::find()->where(['status'=>1,'user_id'=>$modelUser->id])->one();
    	if($modelPerson){
      		$firstName = $modelPerson->contact_person;
    	}
    	$modelDepartmentHead = DepartmentHead::find()->where(['status'=>1,'email'=>$email])->one();
    	if($modelDepartmentHead){
      		$firstName = $modelDepartmentHead->name;
    	}
    	$modelBdm = Bdm::find()->where(['status'=>1,'email'=>$email])->one();
    	if($modelBdm){
      		$firstName = $modelBdm->name;
    	}
		$to = [
			'first-name' => $firstName,
			'name' => $firstName,
			'email' => $email,
		];
    	$from = [
      		'email'=>'noreply@crowdworksindia.com'
    	];
		$params = [
			'heading'=>'Reset Your Password',
			'name'=>$firstName,
			'reset-token'=>$modelUser->password_reset_token,
		];
		$view = 'password-reset-mail';
		$subject = 'Reset your password';
		$heading = 'Reset password';
		$message = '';
		$this->sendMail($from,$to,$subject,$message,$view,$params);
  	}


  	public function sendNewNotificationMail($model,$email) {
		$firstName = $email;
		$to = [
			'first-name' => $firstName,
			'name' => $firstName,
			'email' => $email,
		];
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$params = [
			'heading'=>'New Notification',
			'name'=>$firstName
		];
		$view = 'notification';
		$subject = 'New Notification';
		$heading = 'New Notification';
		$message = '';
		$this->sendMail($from,$to,$subject,$message,$view,$params);
  	}


  	public function sendListingStatus($model,$email) {
		$firstName = $email;
		$to = [
			'first-name' => $firstName,
			'name' => $firstName,
			'email' => $email,
		];
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$params = [
			'heading'=>'Product Listing Status Updated',
			'name'=>$firstName,
			'status' => $model->listing_status
		];
		$view = 'listing-status';
		$subject = 'Product Listing Status Updated';
		$heading = 'Product Listing Status Updated';
		$message = '';
		$this->sendMail($from,$to,$subject,$message,$view,$params);
  	}


  	public function sendNewTradeAgreementMail($email) {
		$firstName = $email;
		$to = [
			'first-name' => $firstName,
			'name' => $firstName,
			'email' => $email,
		];
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$params = [
			'heading'=>'New Trade Agreement',
			'name'=>$firstName
		];
		$view = 'trade-agreement';
		$subject = 'New Trade Agreement';
		$heading = 'New Trade Agreement';
		$message = '';
		$this->sendMail($from,$to,$subject,$message,$view,$params);
  	}


  	public function sendNewProposalMail($email) {
		$firstName = $email;
		$to = [
			'first-name' => $firstName,
			'name' => $firstName,
			'email' => $email,
		];
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$params = [
			'heading'=>'New Trade Proposal',
			'name'=>$firstName
		];
		$view = 'proposal';
		$subject = 'New Trade Proposal';
		$heading = 'New Trade Proposal';
		$message = '';
		$this->sendMail($from,$to,$subject,$message,$view,$params);
  	}

  	public function sendOtp($modelPerson,$otp) {
		$modelUser = Account::find()->where(['status'=>1,'id'=>$modelPerson->account_id])->one();
		$email = $modelPerson->email;
		$firstName = $modelPerson->name;
		$to = [
			'first-name' => $firstName,
			'name' => $firstName,
			'email' => $email,
		];
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$params = [
			'heading'=>'Reset Your Password',
			'name'=>$firstName,
			'otp'=>$otp,
		];
		$view = 'password-reset-mail';
		$subject = 'Reset your password';
		$heading = 'Reset password';
		$message = '';
		$this->sendMail($from,$to,$subject,$message,$view,$params);
  	}


  	public function sendMail($from,$to,$subject,$message,$view='default-mail',$params =[],$ccs=[],$bccs=[]) {
    	$view = '/mail/'.$view;
		Yii::$app->controller->layout = 'email';
		$view = Yii::$app->controller->renderPartial($view,['params'=>$params]);
		$message=$view;
		$boundary = md5("random");
		$separator = md5(time());
		$eol = "\r\n";
		$headers = "From: Crowd Works India Foundation <noreply@crowdworksindia.com>" . $eol;
		$headers .= "MIME-Version: 1.0" . $eol;
		$headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;
		$headers .= "Content-Transfer-Encoding: 7bit" . $eol;
		$body = "--" . $separator . $eol;
		$body .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
		$body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";  
		$body .= $message . $eol;
		$to = $to['email'];
    	mail($to, $subject, $body, $headers);
 	}


 	public function sendMailOld($from,$to,$subject,$message,$view='default-mail',$params =[],$ccs=[],$bccs=[], $attachment = null) {
		$sendGrid = Yii::$app->sendGrid;
		$fromEmail = $from['email'];
		$toEmail = $to['email'];
		$paramsTemp = [
			'from' => $from,
			'to' => $to,
			'subject' => $subject,
			'message' => $message
		];
		$params = array_merge($params, $paramsTemp);
		$sendGrid->view->params['from'] = $from;
		$sendGrid->view->params['to'] = $to;
		$sendGrid->view->params['subject'] = $subject;
		foreach($params as $param => $val) {
			$sendGrid->view->params[$param] = $val;
		}
		$message = $sendGrid->compose($view,['params'=>$params]);
		$message->setFrom($fromEmail)->setTo($toEmail)->setSubject($subject);
		$message->getSendGridMessage()->setCcs($ccs);
		$message->getSendGridMessage()->setBccs($bccs);
		if($attachment){
			$message->attach($attachment);
		}
  		$response = $message->send($sendGrid);
    	foreach($sendGrid->view->params as $param => $val) { 
      		unset($sendGrid->view->params[$param]);
    	}
 	}


  	protected function findModelPerson($personId)
  	{
      	$modelPerson = Person::find()->where(['status'=>1])->andWhere(['id' => $personId])->one();
        if($modelPerson)
      	{
         	return $modelPerson;
      	}
      	throw new NotFoundHttpException('The requested page does not exist.');
  	}

	public function sendComment($model,$message,$email,$name){
		$to = [
			'name' => $name,
			'email' => $email
		];
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$params = [
			'model' => $model,
			'name'=>$name,
			'message' => $message
		];
 		$view = 'comment';
		$subject = "Response from Crowd Works India Foundation";
  		$this->sendMail($from,$to,$subject,$message,$view,$params);
	}

// 	public function sendReceipt($fundraiserId=null,$email,$name,$amount){
// 		$title = 'Crowd Works India Foundations';
// 		if($fundraiserId){
// 			$model = FundraiserScheme::find()->where(['id'=>$fundraiserId])->one();
// 			if($model){
// 				$title = $model->title;
// 			}
// 		}
// 		$to = [
// 			'name' => $name,
// 			'email' => $email
// 		];
// 		$from = [
// 			'email'=>'crowdworksindia@gmail.com'
// 		];
// 		$message = '';
// 		$params = [
// 			'title' => $title,
// 			'name'=>$name,
// 			'message' => $message,
// 			'amount' => $amount
// 		];
//  		$view = 'receipt';
// 		$subject = "Crowd Works India Foundation Donation Receipt";
//   		$this->sendMail($from,$to,$subject,$message,$view,$params);
// 	}

	public function sendSignUpUser($modelUser){
		$to = [
			'name' => $modelUser->name,
			'email' => $modelUser->email
		];
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$message = 'You are Registered successfully, Welcome to Crowd Works India Foundations';
		$params = [
			'name'=>$modelUser->name,
			'message' => $message,
		];
 		$view = 'sign-up-user';
		$subject = "Crowd Works India Foundations";
  		$this->sendMail($from,$to,$subject,$message,$view,$params);
	}
	
	public function sendSignUpAdmin($modelUser){
		$model = User::find()->where(['role'=>'super-admin','status'=>1])->one();
		$to = [
			'name' => $model->name,
			'email' => $model->email
		];	
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$message = 'New user registered to Crowd Works India Foundations.';
		$params = [
			'name'=>$model->name,
			'message' => $message,
		];
 		$view = 'sign-up-admin';
		$subject = "New User Registered.";
  		$this->sendMail($from,$to,$subject,$message,$view,$params);
	}
	public function sendFundraiserUser($modelFundraiser){
		$model = User::find()->where(['id'=>$modelFundraiser->created_by])->one();
		$to = [
			'name' => $model->name,
			'email' => $model->email
		];	
		$from = [
			'email'=>'noreply@crowdworksindia.org'
		];
		$message = 'We are Thrilled to wlcome you to the Crwod Works India Foundation Fundraising Platform! Your initiative to make a positive impact on our community is truly commendable.<br>
			Here`s what you can expect from our platform:<br>
						
						1. Create Your Fundraiser: Start by setting up your fundraiser with all the necessary details.<br>
                        2. Reach Your Goal: Our platform is designed to help you reach your fundraising target effectively.<br>
                        3. Engage Supporters: Connect with your supporters and keep them updated on your progress.<br>
                        4. Fundraising Tools: We provide you with various tools and resources to boost your fundraising efforts.<br>
						
						If you have any questions or need assistance, please don`t hesitate to reach out to our support team at care@crowdworksindia.org<br>
						
						Thank you for joining us in making a difference!<br>

						Best regards,<br>
						Crowd Works India Foundation<br>
						crowdworksindia.org';
		$params = [
			'name'=>$model->name,
			'message' => $message,
		];
 		$view = 'fundraiser-user';
		$subject = "Welcome to Crowd Works India Foundation Fundraising Platform!.";
  		$this->sendMail($from,$to,$subject,$message,$view,$params);
	}
	public function sendFundraiserAdmin($modelFundraiser){
		$model = User::find()->where(['id'=>$modelFundraiser->created_by])->one();
		$admin = User::find()->where(['role'=>'super-admin','status'=>1])->one();
		$to = [
			'name' => $admin->name,
			'email' => $admin->email
		];	
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$message = 'New fundraiser scheme created by '.$model->name.' Please check admin panel for details.';
		$params = [
			'name'=>$admin->name,
			'message' => $message,
		];
 		$view = 'fundraiser-admin';
		$subject = "Fundraiser Scheme Created.";
  		$this->sendMail($from,$to,$subject,$message,$view,$params);
	}
	public function sendFundraiserApprove($modelFundraiser){
		$model = User::find()->where(['id'=>$modelFundraiser->created_by])->one();
		$to = [
			'name' => $model->name,
			'email' => $model->email
		];	
		$from = [
			'email'=>'noreply@crowdworksindia.org'
		];
		$message ='
		We are delighted to inform you that your fundraiser on Crowd Works India Foundation Fundraising Platform has been approved! This is a significant milestone in your journey to make a positive impact on the cause you care about.<br>
		
		Now that your fundraiser is live, it`s time to start spreading the word and engaging your supporters. Here are a few tips to maximize your success:<br>
		
		1. Share Your Story: Craft a compelling story that explains why your cause matters.<br>
		2. Leverage Social Media: Use social platforms to connect with your community and promote your fundraiser.<br>
		3. Regular Updates: Keep your supporters informed about your progress and any upcoming events.<br>
		4. Say Thank You: Don`t forget to express your gratitude to your donors and supporters.<br>
		
		If you have any questions or need assistance, please don`t hesitate to reach out to our support team at care@crowdworksindia.org <br> 
		
		Thank you for your dedication to your cause and for choosing Crowd Works India Foundation Fundraising Platform!<br>
		
		Best regards,<br>
		Crowd Works India Foundation<br>
		crowdworksindia.org';
		$params = [
			'name'=>$model->name,
			'message' => $message,
		];
 		$view = 'fundraiser-approve';
		$subject = "Your Fundraiser on Crowd Works India Foundation Fundraising Platform is Approved!";
  		$this->sendMail($from,$to,$subject,$message,$view,$params);
	}
	public function sendFundraiserReject($modelFundraiser){
		$model = User::find()->where(['id'=>$modelFundraiser->created_by])->one();
		$to = [
			'name' => $model->name,
			'email' => $model->email
		];	
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$message = 'Fundraiser scheme rejected.';
		$params = [
			'name'=>$model->name,
			'message' => $message,
		];
 		$view = 'fundraiser-approve';
		$subject = "Fundraiser Scheme Rejected";
  		$this->sendMail($from,$to,$subject,$message,$view,$params);
	}
	
// 	public function sendFundraiserApprove($modelCampaign)
// 	{
// 		$model = User::find()->where(['id'=>$modelCampaign->created_by])->one();
// 		$to = [
// 			'name' => $model->name,
// 			'email' => $model->email
// 		];	
// 		$from = [
// 			'email'=>'crowdworksindia@gmail.com'
// 		];
// 		$message = 'Fundraiser scheme approved.';
// 		$params = [
// 			'name'=>$model->name,
// 			'message' => $message,
// 		];
//  		$view = 'fundraiser-approve';
// 		$subject = "Fundraiser Scheme Approved";
//   		$this->sendMail($from,$to,$subject,$message,$view,$params);
// 	}
	
	public function sendCampaignCancelled($modelCampaign){
		$model = User::find()->where(['id'=>$modelCampaign->created_by])->one();
		$to = [
			'name' => $model->name,
			'email' => $model->email
		];	
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$message = 'Campanign Cancelled.';
		$params = [
			'name'=>$model->name,
			'message' => $message,
		];
 		$view = 'campaign-approve';
		$subject = "Campanign Cancelled";
  		$this->sendMail($from,$to,$subject,$message,$view,$params);
	}
	public function sendDonationUser($email,$name,$amount){
		$to = [
			'name' => $name,
			'email' => $email
		];	
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$message = 'Thank you for your great generosity! We at Crowd Works India Foundation, 
		greatly appreciate your donation of Rs '.$amount.', 
		and your sacrifice.';
		$params = [
			'name'=>$name,
			'message' => $message,
		];
 		$view = 'donation';
		$subject = "Donated succesfully";
  		$this->sendMail($from,$to,$subject,$message,$view,$params);
	}
	public function sendDonationAdmin($email,$name,$amount){
		$model = User::find()->where(['role'=>'super-admin','status'=>1])->one();
		$to = [
			'name' => $model->name,
			'email' => $model->email
		];	
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$message = $name.' Donated for amount '.$amount;
		$params = [
			'name'=>$model->name,
			'message' => $message,
		];
 		$view = 'donation';
		$subject = "Donated succesfully";
  		$this->sendMail($from,$to,$subject,$message,$view,$params);
	}
	
	public function sendAgencyPageLink($model)
	{
	    $modelAgency = Agency::find()->where(['id'=>$model->agency_id])->one();
		$to = [
			'name' => $modelAgency->name,
			'email' => $modelAgency->email
		];	
		$from = [
			'email'=>'noreply@crowdworksindia.com'
		];
		$message = 'Agency Payment Link'.$model->landing_page_url;
		$params = [
			'name'=>$modelAgency->name,
			'message' => $message,
		];
	    $view = 'agency-mail';
		$subject = "Agency Payment Link";
  		$this->sendMail($from,$to,$subject,$message,$view,$params);
	}
}