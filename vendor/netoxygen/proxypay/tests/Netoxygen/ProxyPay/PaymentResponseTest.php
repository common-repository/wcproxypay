<?php
use AspectMock\Test as test;
use Netoxygen\ProxyPay\PaymentResponse;
use Netoxygen\ProxyPay\ParametersFilter;
use Netoxygen\ProxyPay\ShaComposer;

/**
 * Tests the ProxyPayResponse class
 *
 * @coversDefaultClass Netoxygen\ProxyPay\PaymentResponse
 */
class PaymentResponseTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        test::clean(); // remove all registered test doubles
    }

    /**
     * @covers \Netoxygen\ProxyPay\PaymentResponse::__construct
     */
    function test_constructor()
    {
        $parameters = [ "SELLER" => "test" ];
        $key        = "fake-key";

        $filter   = test::double(ParametersFilter::class, [ 'filter_out_parameters' => $parameters ]);
        $composer = test::double(ShaComposer::class, []);

        $request = new PaymentResponse($parameters, $key);

        $filter->verifyInvokedOnce('filter_out_parameters', [ $parameters ]);
        $composer->verifyInvokedOnce('__construct', [ $key ]);
    }
}
