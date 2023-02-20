<?php

return [
    'name' => 'Fac',
    'key' => 'payment_fac',
    'support_subscription' => false,
    'support_ecommerce' => true,
    'support_marketplace' => true,
    'manage_remote_plan' => false,
    'manage_remote_product' => false,
    'manage_remote_sku' => false,
    'manage_remote_order' => false,
    'supports_swap' => false,
    'supports_swap_in_grace_period' => false,
    'require_invoice_creation' => false,
    'require_plan_activation' => false,
    'capture_payment_method' => false,
    'require_default_payment_set' => false,
    'can_update_payment' => false,
    'create_remote_customer' => false,
    'require_payment_token' => false,
    'default_subscription_status' => 'pending',
    'offline_management' => false,
    'settings' => [
        'live_merchant_id' => [
            'label' => 'Fac::labels.settings.live_merchant_id',
            'type' => 'text',
            'required' => false,
        ],
        'live_merchant_password' => [
            'label' => 'Fac::labels.settings.live_merchant_password',
            'type' => 'text',
            'required' => false,
        ],
        'sandbox_mode' => [
            'label' => 'Fac::labels.settings.sandbox_mode',
            'type' => 'boolean'
        ],
        'sandbox_merchant_id' => [
            'label' => 'Fac::labels.settings.sandbox_merchant_id',
            'type' => 'text',
            'required' => false,
        ],
        'sandbox_merchant_password' => [
            'label' => 'Fac::labels.settings.sandbox_merchant_password',
            'type' => 'text',
            'required' => false,
        ],
        'acquirerId' => [
            'label' => 'Fac::labels.settings.acquirerId',
            'type' => 'text',
            'required' => false,
        ],
//        'three_des' => [
//            'label' => 'Fac::labels.settings.three_des',
//            'type' => 'boolean',
//            'required' => false,
//        ],
//        'merchant_response_url' => [
//            'label' => 'Fac::labels.settings.merchant_response_url',
//            'type' => 'text',
//            'required' => false,
//        ],
    ],
    'events' => [
    ],
    'webhook_handler' => '',
];
