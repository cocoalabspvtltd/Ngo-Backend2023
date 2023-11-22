<?php 
$role = Yii::$app->user->identity->role;
if($role == 'super-admin'){
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'Dashboard', 'icon' => 'circle-o', 'url' => ['site/index']],
                    ['label' => 'Campaign Categories', 'icon' => 'circle-o', 'url' => ['campaign/index']],
                    ['label' => 'Settings', 'icon' => 'circle-o', 'url' => ['setting/index']],
                    // ['label' => 'Fundraiser Scheme', 'icon' => 'circle-o', 'url' => ['campaign-fundraiser-scheme/index']],
                    [
                        'label' => 'Fundraiser Scheme',
                        'icon' => 'share',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Fundraiser Scheme List', 'icon' => 'circle-o', 'url' => ['campaign-fundraiser-scheme/index']],
                            // ['label' => 'Fundraiser Update Request', 'icon' => 'circle-o', 'url' => ['fundraiser-scheme-update-request/index']],
                            ['label' => 'Comment History', 'icon' => 'circle-o', 'url' => ['fundraiser-comment/index']],
                            // ['label' => 'Subscriptions', 'icon' => 'circle-o', 'url' => ['subscription/index']],
                            ['label' => 'Fundraiser Comments', 'icon' => 'circle-o', 'url' => ['fundraiser-scheme-comment/index']],
                        ]
                    ],
                    ['label' => 'Campaigns List', 'icon' => 'circle-o', 'url' => ['fundraiser-scheme/index']],
                    ['label' => 'Volunteer Requests', 'icon' => 'circle-o', 'url' => ['volunteer-requests/index']],
                    ['label' => 'Users List', 'icon' => 'circle-o', 'url' => ['users/index']],
                    ['label' => 'Contact Us', 'icon' => 'circle-o', 'url' => ['contact-us/index']],
                    ['label' => 'Partner Requests', 'icon' => 'circle-o', 'url' => ['partner/index']],
                    ['label' => 'FAQ', 'icon' => 'circle-o', 'url' => ['faq/index']],
                    // ['label' => 'Donations', 'icon' => 'circle-o', 'url' => ['donation/index']],
                    [
                        'label' => 'Donations',
                        'icon' => 'share',
                        'url' => '#',
                        'items' => [
                           // ['label' => 'ALL', 'icon' => 'circle-o', 'url' => ['donation/all-donations']],
                            ['label' => 'NGO', 'icon' => 'circle-o', 'url' => ['donation/index']],
                            ['label' => 'Others', 'icon' => 'circle-o', 'url' => ['other-donation/index']],
                            ['label' => 'Campaign', 'icon' => 'circle-o', 'url' => ['other-donation/campaignindex']],
                            // ['label' => 'Agency', 'icon' => 'circle-o', 'url' => ['other-donation/agency-donation-history']],
                              ['label' => 'Agency', 'icon' => 'circle-o', 'url' => ['other-donation/agencyindex']],
                            // ['label' => 'Subscriptions', 'icon' => 'circle-o', 'url' => ['subscription/index']],
                        ]
                    ],
                    ['label' => 'Chatbox', 'icon' => 'circle-o', 'url' => ['feedbacks/index']],
                    ['label' => 'Media', 'icon' => 'circle-o', 'url' => ['media/index']],
                    ['label' => 'Our Team', 'icon' => 'circle-o', 'url' => ['our-team/index']],
                    ['label' => 'Loan', 'icon' => 'circle-o', 'url' => ['loan-donation/index']],
                    ['label' => 'Lends', 'icon' => 'circle-o', 'url' => ['loan/index']],
                    ['label' => 'Agencies', 'icon' => 'circle-o', 'url' => ['agency/index']],
                    ['label' => 'Agency Landing Pages', 'icon' => 'circle-o', 'url' => ['agency-landing-page/index']],
                    
                    [
                        'label' => 'Accounts',
                        'icon' => 'share',
                        'url' => '#',
                        'items' => [
                            ['label' => '80G Form Requests', 'icon' => 'circle-o', 'url' => ['certificate/index']],
                            ['label' => 'Amount Transfer Requests', 'icon' => 'circle-o', 'url' => ['transfer-request/index']],
                            ['label' => 'Subscriptions', 'icon' => 'circle-o', 'url' => ['subscription/index']],
                        ]
                    ],
                    [
                        'label' => 'Master',
                        'icon' => 'share',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Relation', 'icon' => 'circle-o', 'url' => ['/relation-master'],],
                            ['label' => 'Point', 'icon' => 'circle-o', 'url' => ['/point'],],
                            ['label' => 'Pricing', 'icon' => 'circle-o', 'url' => ['/pricing-master'],],
                        ]
                    ],
                    // ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
                ],
            ]
        ) ?>

    </section>

</aside>
<?php 
}
else if($role == 'agency')
{
?>
    <aside class="main-sidebar">

    <section class="sidebar">

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'Profile', 'icon' => 'circle-o', 'url' => ['agency/agency-profile']],
                    ['label' => 'Payment History', 'icon' => 'circle-o', 'url' => ['agency/payment-history']]
    
                ],
            ]
        ) ?>

    </section>

</aside>
<?php
}
?>   