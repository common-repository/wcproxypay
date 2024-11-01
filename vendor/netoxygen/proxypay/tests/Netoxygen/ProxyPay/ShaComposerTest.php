<?php

use Netoxygen\ProxyPay\ShaComposer;

/**
 * Tests the ShaComposer class
 *
 * @coversDefaultClass Netoxygen\ProxyPay\ShaComposer
 */
class ShaComposerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers \Netoxygen\ProxyPay\ShaComposer::compose
     * @covers \Netoxygen\ProxyPay\ShaComposer::__construct
     * @dataProvider provide_sha256_request
     */
    function test_compose($key, $parameters, $expected_sha)
    {
        $composer = new ShaComposer($key);
        $this->assertEquals($expected_sha, $composer->compose($parameters));
    }

    function provide_sha256_request()
    {
        $key = "ea8aee5ae138d076f2d572298e871ff947a8b8815dbb1544edb18def699da723";

        $parameters1   = $this->create_in_parameters_set();
        $expected_sha1 = "5440b26de45174f4a51a35a4acb209ad2fc83fac71a801bfc0afa53b6ad2a002";

        $parameters2   = $this->create_out_parameters_set();
        $expected_sha2 = "d5260cfcb77afa97ae63ec25ab9e7eb80036ff487afea1d2c9a9b98d3822cf85";

        return [
            [ $key, $parameters1, $expected_sha1 ],
            [ $key, $parameters2, $expected_sha2 ],
        ];
    }

    function create_in_parameters_set()
    {
        return [
            "SELLER"        => "test",
            "AMOUNT"        => 100.00,
            "DESCRIPTION"   => "Test transaction",
            "SUCCESS_URL"   => "https://balidra-payment.neto2.net/payment/test_success",
            "ERROR_URL"     => "https://balidra-payment.neto2.net/payment/test_error",
            "CANCEL_URL"    => "https://balidra-payment.neto2.net/payment/test_cancel",
            "FULLNAME"      => "Belkacem Alidra",
            "ADDRESS"       => "Avenue d'aïre 56",
            "ZIP"           => "1203",
            "CITY"          => "Genève",
            "COUNTRY"       => "CH",
            "EMAIL"         => "support@netoxygen.ch",
            ShaComposer::SHA_SIGN_FIELD => "THIS MUST BE FILTERED"
        ];
    }

    function create_out_parameters_set()
    {
        return [
            "SELLER" => "test",
            "AMOUNT" => 100.00,
            "TRX_ID" => 42,
            "STATUS" => "SUCCESS"
        ];
    }
}
