<?php
define('UPLOADS_PATH','https://crowdworksindia.org/test/common/uploads');

define('SMS_KEY','283326AYHR8CW2Rakl5d19b46c');
define('SMS_USERID','WMSMGMT');
define('SECRET_KEY','sec!ReT413*&');
define('ISSUER','localhost');

/*********** TEST KEY *****************/
define('KEY_ID','rzp_test_rnKTnq3SXxJdFA');
define('KEY_SECRET','Yg2XnWsNuK4RYuY7e6HfYHhz');
/*********** TEST KEY *****************/

/*********** LIVE KEY *****************/
 // define('KEY_ID','rzp_live_oI8UP7XG9J5xYl');
  //define('KEY_SECRET','Nccs3h9GVVbbjvL2lyfqQa6J');
/*********** TEST KEY *****************/

return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,

    'uploads_path' => '@common/uploads/',

    'upload_path_campaign_icons' => '@uploads/campaign-icons/',
    'base_path_campaign_icons' => UPLOADS_PATH.'/campaign-icons/',

    'upload_path_fundraiser_images' => '@uploads/fundraiser-images/',
    'base_path_fundraiser_images' => UPLOADS_PATH.'/fundraiser-images/',

    'upload_path_profile_images' => '@uploads/profile/',
    'base_path_profile_images' => UPLOADS_PATH.'/profile/',

    'upload_path_fundraiser_documents' => '@uploads/fundraiser-documents/',
    'base_path_fundraiser_documents' => UPLOADS_PATH.'/fundraiser-documents/',
    
    'upload_path_media_images' => '@uploads/media-images/',
    'base_path_media_images' => UPLOADS_PATH.'/media-images/',
    
    'upload_path_loan_images' => '@uploads/loan-images/',
    'base_path_loan_images' => UPLOADS_PATH.'/loan-images/',
    
    'upload_path_PAN_images' => '@uploads/PAN-images/',
    'base_path_PAN_images' => UPLOADS_PATH.'/PAN-images/',
    
    'upload_path_80G_Document' => '@uploads/80GDocument',
    'base_path_80G_Documens' => UPLOADS_PATH.'/80GDocument/',
];
