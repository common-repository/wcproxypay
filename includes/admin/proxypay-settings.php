<?php
if (! defined('ABSPATH')) {
        exit;
}

return apply_filters(
    'wc_proxypay_settings',
    array(
        'enabled' => array(
            'title'       => __('Enable/Disable', 'wc_proxypay'),
            'label'       => __('Enable Proxypay', 'wc_proxypay'),
            'type'        => 'checkbox',
            'description' => '',
            'default'     => 'no',
            ),
        'title' => array(
            'title'       => __('Title', 'wc_proxypay'),
            'type'        => 'text',
            'description' => __('This controls the title which the user sees during checkout.', 'wc_proxypay'),
            'default'     => 'Proxypay',
            'desc_tip'    => true,
            ),
        'description' => array(
            'title'       => __('Description', 'wc_proxypay'),
            'type'        => 'text',
            'description' => __('This controls the description which the user sees during checkout', 'wc_proxypay'),
            'default'     => __('Pay with your credit card via Proxypay.', 'wc_proxypay'),
            'desc_tip'    => true,
            ),
        'testmode' => array(
            'title'       => __('Test mode', 'wc_proxypay'),
            'label'       => __('Enable Test Mode', 'wc_proypay'),
            'type'        => 'checkbox',
            'description' => __('Place the payment gateway in test mode', 'wc_proxypay'),
            ),
        'fulldesc' => array(
            'title'       => __('Full description', 'wc_proxypay'),
            'label'       => __('Enable full description', 'wc_proxypay'),
            'type'        => 'checkbox',
            'description' => __('By default the plugin only send order ID to proxypay. By checking this box you will send the full cart content.', 'wc_prixypay'),
            'default'     => 'no'
            ),
        'label' => array(
            'title'       => __('Configuration CHF:', 'wc_proxypay'),
            'type'        => 'hidden',
            ),
        'seller' => array(
            'title'       => __('Seller', 'wc_proxypay'),
            'type'        => 'text',
            'description' => __('This is your seller account on proxypay', 'wc_proxypay'),
            'default'     => '',
            'desc_tip'    => true,
            ),
        'sha_in' => array(
            'title'       => __('Sha_in', 'wc_proxypay'),
            'type'        => 'text',
            'description' => __('This key is used to sign your forms value submitted to proxypay', 'wc_proxypay'),
            'default'     => '',
            'desc_tip'    => true,
            ),
        'sha_out' => array(
            'title'       => __('Sha_out', 'wc_proxypay'),
            'type'        => 'text',
            'description' => __('This key is used to check proxypay callbacks', 'wc_proxypay'),
            'default'     => '',
            'desc_tip'    => true,
            ),
        'label_euro' => array(
            'title'       => __('Configuration EURO:', 'wc_proxypay'),
            'type'        => 'hidden',
            ),
        'seller_euro' => array(
            'title'       => __('Seller', 'wc_proxypay'),
            'type'        => 'text',
            'description' => __('This is your seller account on proxypay', 'wc_proxypay'),
            'default'     => '',
            'desc_tip'    => true,
            ),
        'sha_in_euro' => array(
            'title'       => __('Sha_in', 'wc_proxypay'),
            'type'        => 'text',
            'description' => __('This key is used to sign your forms value submitted to proxypay', 'wc_proxypay'),
            'default'     => '',
            'desc_tip'    => true,
            ),
        'sha_out_euro' => array(
            'title'       => __('Sha_out', 'wc_proxypay'),
            'type'        => 'text',
            'description' => __('This key is used to check proxypay callbacks', 'wc_proxypay'),
            'default'     => '',
            'desc_tip'    => true,
            )
    )
);
