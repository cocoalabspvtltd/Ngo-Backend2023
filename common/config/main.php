<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@uploads' => '@common/uploads',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'email' => [
            'class'=>'backend\components\EmailComponent'
        ],
        'notification' => [
            'class'=>'backend\components\NotificationComponent'
        ],
        'onesignal' => [
			'class' => '\rocketfirm\onesignal\OneSignal',
			'appId' => 'e132f07e-4963-4e76-97cb-4479afdb7cfb',
			'apiKey' => 'ZDkyZTA0ODktZjdkZC00Yzc0LWIzNDctZTI0OTA4N2E2OTU5',
            // 'appIdWeb' => '28633676-3bf9-460d-bb1c-3de2aedec358',
			// 'apiKeyWeb' => 'NjkzMmE1MjMtNGE0Yy00OWJmLWJlNjEtNGRkMDEyMGI4MmI1',
		],
    ],
];
