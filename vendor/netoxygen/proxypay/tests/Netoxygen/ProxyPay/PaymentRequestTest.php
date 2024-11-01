<?php
use AspectMock\Test as test;
use Netoxygen\ProxyPay\PaymentRequest;
use Netoxygen\ProxyPay\ParametersFilter;
use Netoxygen\ProxyPay\ShaComposer;

/**
 * Tests the ProxyPayRequest class
 *
 * @coversDefaultClass Netoxygen\ProxyPayPaymentRequest
 */
class PaymentRequestTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        test::clean(); // remove all registered test doubles
    }

    /**
     * @covers \Netoxygen\ProxyPay\PaymentRequest::__construct
     */
    function test_constructor()
    {
        $parameters = [ "SELLER" => "test" ];
        $key        = "fake-key";

        $filter   = test::double(ParametersFilter::class, [ 'filter_in_parameters' => $parameters ]);
        $composer = test::double(ShaComposer::class, []);

        $request = new PaymentRequest($parameters, $key);

        $filter->verifyInvokedOnce('filter_in_parameters', [ $parameters ]);
        $composer->verifyInvokedOnce('__construct', [ $key ]);
    }
}
