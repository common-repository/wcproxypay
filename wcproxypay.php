<?php
/*
 * Plugin Name: Woocommerce Proxypay
 * Plugin URI: https://proxypay.com
 * Description: Proxypay integration
 * Version: 1.3.1
 * Author: Net Oxygen Sàrl
 * Author URI: https://netoxygen.ch
 * License: GPLv3
 */

require dirname(__FILE__) . '/vendor/autoload.php';

use Netoxygen\ProxyPay\PaymentRequest;
use Netoxygen\ProxyPay\PaymentResponse;

add_action('plugins_loaded', 'woocommerce_gateway_name_init', 0);

function woocommerce_gateway_name_init()
{
    if ( !class_exists( 'WC_Payment_Gateway' ) ) return;

    /**
      * Localisation
     */
    //load_plugin_textdomain('wc-gateway-name', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');

    /**
      * Gateway class
      */
    class WC_Proxypay extends WC_Payment_Gateway
    {
        /**
         * Proxypay Seller
         *
         * @var string
        **/
        public $seller;

        /**
         * Proxypay sha_out
         *
         * @var string
        **/
        public $sha_in;

        /**
         * Proxypay sha_out
         *
         * @var string
        **/
        public $sha_out;

        /**
         * Proxypay test mode
         *
         * @var bool
        **/
        public $testmode;

        /**
         * Proxypay full desc of order (cart content)
         *
         * @var bool
        **/
        public $fulldesc;
        /**
         * Proxypay callback url
         *
         * @var string
        **/
        protected $transaction_response_handler_url;
        /**
         * Constructor
        **/
        public function __construct()
        {
            $this->id           = 'proxypay';
            $this->has_fields   = true;
            $this->method_title = 'Proxypay';

            $this->init_form_fields();
            $this->init_settings();

            $this->title        = $this->get_option('title');
            $this->description  = $this->get_option('description');
            $this->enabled      = $this->get_option('enabled');
            $this->sha_in       = $this->get_option('sha_in');
            $this->sha_out      = $this->get_option('sha_out');
            $this->seller       = $this->get_option('seller');
            $this->sha_in_euro  = $this->get_option('sha_in_euro');
            $this->sha_out_euro = $this->get_option('sha_out_euro');
            $this->seller_euro  = $this->get_option('seller_euro');
            $this->testmode     = 'yes' === $this->get_option('testmode');
            $this->fulldesc     = 'no' === $this->get_option('fulldesc');
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            // Hook to create form and redirect to proxypay
            add_action('woocommerce_receipt_' . $this->id, array(
                        $this,
                        'pay_for_order'
                        ));
            // Hook to handle callback from proxypay
            add_action('woocommerce_api_wc_gateway_' . $this->id, array(
                        $this,
                        'check_proxypay_response'
                        ));
        }

        public function init_form_fields()
        {
            $this->form_fields = require(dirname(__FILE__) . '/includes/admin/proxypay-settings.php');
        }

        public function process_payment($order_id)
        {
            $order = wc_get_order($order_id);

            // Return proxypay redirect
            return array(
                    'result' => 'success',
                    'redirect' => $order->get_checkout_payment_url(true)
                );
        }

        // here, prepare your form and submit it to the required URL
        public function pay_for_order($order_id)
        {
            $order    = wc_get_order($order_id);
            $currency = $order->data['currency'];

            if ($currency === 'EUR') {
                $this->sha_in  = $this->sha_in_euro;
                $this->sha_out = $this->sha_out_euro;
                $this->seller  = $this->seller_euro;
            } elseif ($currency !== 'CHF') {
                return;
            }

            $validation_url = add_query_arg('order_id', $order_id, $this->get_transaction_response_handler_url());
            $order = wc_get_order($order_id);
            echo '<p>' . __('Redirecting to payment provider.', 'wc_proxypay') . '</p>';
            // add a note to show order has been placed and the user redirected
            $order->add_order_note(__('Order placed and user redirected.', 'wc_proxypay'));
            // update the status of the order should need be
            $order->update_status('on-hold', __('Awaiting payment.', 'wc_proxypay'));
            wc_reduce_stock_levels($order_id);
            $description = "Woocommerce $order_id";

            if ($this->fulldesc != 'no') {
                $description .= " : ";
                $items = $order->get_items();
                foreach ($items as $item) {
                    $description .= $item->get_name()." - ";
                }
            }
            // perform a click action on the submit button of the form you are going to return
            wc_enqueue_js('jQuery( "#submit-form" ).click();');
            $request_params = [
                'test'            => $this->testmode,
                'gateway'         => 'datatrans',
                'SELLER'          => $this->seller,
                'AMOUNT'          => $order->get_total(),
                'DESCRIPTION'     => $description,
                'FULLNAME'        => $order->get_formatted_billing_full_name(),
                'ADDRESS'         => $order->get_billing_address_1(),
                'ZIP'             => $order->get_billing_postcode(),
                'CITY'            => $order->get_billing_city(),
                'COUNTRY'         => $order->get_billing_country(),
                'EMAIL'           => $order->get_billing_email(),
                'SUCCESS_URL'     => $validation_url,
                'ERROR_URL'       => $validation_url,
                'CANCEL_URL'      => $validation_url,
                'CALLBACK_URL'    => $validation_url,
                'CALLBACK_METHOD' => 'GET',
            ];

            $request = new PaymentRequest($request_params, $this->sha_in);
            // Add the query parameters signature
            $request_params['SHA_SIGN'] = $request->compose_sha_sign();

            // return your form with the needed parameters
            $form = '<script>
                setTimeout(function() {
                        smoothScroll(\'proxypayForm\');
                        setTimeout(function() {
                                document.getElementById(\'proxypayForm\').submit();
                                }, 300);
                        }, 300);
            </script>
                <form action="' . 'https://pay.proxypay.ch/request/form' . '" method="post" id="proxypayform" target="_top">';
            foreach ($request->get_parameters() as $key => $value) {
                $form.='<input type="hidden" name="'.$key.'" value="'.$value.'" />';
            }
            $form.='
                <input type="hidden" name="test" value="'.$this->testmode.'" />
                <input type="hidden" name="gateway" value="'.$request_params['gateway'].'" />
                <input type="hidden" name="SHA_SIGN" value="'.$request_params['SHA_SIGN'].'" />
                <div class="btn-submit-payment" style="display: none;">
                <button type="submit" id="submit-form"></button>
                </div>
                </form>';
            echo $form;
        }

        public function check_proxypay_response()
        {
            if ((!isset($_GET['order_id'])) || empty($_GET['order_id']) || (!is_numeric($_GET['order_id']))) {
                //We miss order_id, we can not handle proxypay response
            } else {
                $order_id=$_GET['order_id'];
                $order=wc_get_order($order_id);
                if (!$order) {
                    die(__("Order was lost during payment attempt - please inform merchant about WooCommerce EveryPay gateway problem.", 'wc_proxypay'));
                }

                if (get_woocommerce_currency() === 'EUR') {
                    $this->sha_out = $this->sha_out_euro;
                }

                $response = new PaymentResponse($_GET, $this->sha_out);
                if ($response->is_valid()) {
                    // $response signature is verified, now check the transaction status
                    $params         = $response->get_parameters();
                    $payment_status = $params['STATUS'];
                    switch ($payment_status) {
                        case 'SUCCESS':
                            $order->payment_complete();
                            $order->add_order_note(__('Proxypay Transaction approved. Reference: '.$params['TRX_ID'], 'wc_proxypay'));
                            add_post_meta($oder_id, '_transaction_id', $params['TRX_ID'], true);
                            // remember to empty the cart of the user
                            WC()->cart->empty_cart();
                            $redirect_url = $this->get_return_url($order);
                            break;

                        case 'CANCEL':
                            $order->update_status('pending', __('Payment cancelled; Reference: '.$params['TRX_ID'], 'wc_proxypay'));
                            //Restock
                            /* fix: double the restocking
                            foreach ($order->get_items() as $item_id => $item) {
                                // Get an instance of corresponding the WC_Product object
                                $product = $item->get_product();
                                $qty     = $item->get_quantity(); // Get the item quantity
                                wc_update_product_stock($product, $qty, 'increase');
                            }*/
                            $order->add_order_note(__('Payment cancelled. Reference: '.$params['TRX_ID'], 'wc_proxypay'));
                            $redirect_url = wc_get_checkout_url();
                            break;

                        case 'ERROR':   /* FALLTHROUGH */
                            $order->update_status('failed', __('An error occurred while processing the payment!', 'wc_proxypay'));
                            wc_add_notice( __('An error occurred while processing the payment. Please try once again.', 'wc_proxypay'),'error');
                             $redirect_url = wc_get_checkout_url();
                             break;

                        default:
                            // An error occured
                            $order->update_status('failed', __('An error occurred while processing the payment response, please notify merchant!', 'wc_proxypay'));
                            $redirect_url = wc_get_checkout_url();
                            break;
                    }
                    wp_redirect($redirect_url);
                } else {
                    // Bad request: throw away
                }
            }
        }

        /**
         * Returns the WC API URL for this gateway, based on the current protocol
         *
         */
        public function get_transaction_response_handler_url()
        {
            if ($this->transaction_response_handler_url) {
                return $this->transaction_response_handler_url;
            }
            $this->transaction_response_handler_url = add_query_arg('wc-api', 'wc_gateway_'.$this->id, home_url('/'));
            // make ssl if needed
            if (wc_checkout_is_https()) {
                $this->transaction_response_handler_url = str_replace('http:', 'https:', $this->transaction_response_handler_url);
            }
            return $this->transaction_response_handler_url;
        }
    }
    /**
     * Add the Gateway to WooCommerce
     **/
    function woocommerce_add_gateway_name_gateway($methods)
    {
        $currencies = ['EUR', 'CHF'];
        // Does not offer Proxypay if the currency is not EUR or CHF
        if (!in_array(get_woocommerce_currency(), $currencies)) {
            return $methods;
        }

        $methods[] = 'WC_Proxypay';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'woocommerce_add_gateway_name_gateway');
}
