<?php

use Netoxygen\ProxyPay\ShaComposer;;
use Netoxygen\ProxyPay\ParametersFilter;

/**
 * Tests the ParametersFilter class
 *
 * @coversDefaultClass  Netoxygen\ProxyPay\ParametersFilter
 */
class ParametersFilterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::filter_in_parameters
     */
    function test_filter_in_parameters()
    {
        $parameters = [
            "seller"                    => "test",
            "AMOUNT"                    => 100.00,
            "description"               => "Test transaction",
            "SUCCESS_URL"               => "https://balidra-payment.neto2.net/payment/test_success",
            "error_url"                 => "https://balidra-payment.neto2.net/payment/test_error",
            "CANCEL_URL"                => "https://balidra-payment.neto2.net/payment/test_cancel",
            "CALLBACK_URL"              => "https://balidra-payment.neto2.net/callback_url",
            "CALLBACK_METHOD"           => "POST",
            "fullname"                  => "Belkacem Alidra",
            "ADDRESS"                   => "Avenue d'aïre 56",
            "zip"                       => "1203",
            "CITY"                      => "Genève",
            "country"                   => "CH",
            "EMAIL"                     => "support@netoxygen.ch",
            ShaComposer::SHA_SIGN_FIELD => "a223a1812c02a78cd79c6f0b2e1425ad",
            "UNALLOWED_PARAM"           => "..."
        ];

        $filter = new ParametersFilter();
        $filtered_parameters = $filter->filter_in_parameters($parameters);

        unset($parameters["UNALLOWED_PARAM"]);
        $expected_filtered_parameters = array_change_key_case($parameters, CASE_UPPER);

        $this->assertEquals($expected_filtered_parameters, $filtered_parameters);
    }

    /**
     * @covers ::filter_out_parameters
     */
    function test_filter_out_parameters()
    {
        $parameters = [
            "seller"          => "test",
            "AMOUNT"          => 100.00,
            "trx_id"          => 42,
            "STATUS"          => "SUCCESS",
            "UNALLOWED_PARAM" => "..."
        ];

        $filter              = new ParametersFilter();
        $filtered_parameters = $filter->filter_out_parameters($parameters);

        $expected_filtered_parameters = array_change_key_case($parameters, CASE_UPPER);
        unset($expected_filtered_parameters["UNALLOWED_PARAM"]);

        $this->assertEquals($expected_filtered_parameters, $filtered_parameters);
    }
}
