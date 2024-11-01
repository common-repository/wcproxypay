<?php
use AspectMock\Test as test;
use Netoxygen\ProxyPay\PaymentRequest;
use Netoxygen\ProxyPay\ParametersFilter;
use Netoxygen\ProxyPay\ShaComposer;

/**
 * Tests the PaymentQuery class
 *
 * @coversDefaultClass  Netoxygen\ProxyPay\PaymentQuery
 */
class PaymentQueryTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        test::clean(); // remove all registered test doubles
    }

    /**
     * @covers  \Netoxygen\ProxyPay\PaymentRequest::__construct
     * @covers  \Netoxygen\ProxyPay\PaymentRequest::compose_sha_sign
     */
    function test_compose_sha_sign()
    {
        $parameters   = [ "SELLER" => "test" ];
        $key          = "fake-key";

        $expected_sha = "this-is-not-a-sha-sign";

        $filter   = test::double(ParametersFilter::class, [ 'filter_in_parameters' => $parameters ]);
        $composer = test::double(ShaComposer::class, [ 'compose' => $expected_sha]);

        $request  = new PaymentRequest($parameters, $key);
        $sha_sign = $request->compose_sha_sign();

        $composer->verifyInvokedOnce('compose', [ $parameters ]);

        $this->assertEquals($expected_sha, $sha_sign);
    }

    /**
     * @covers ::is_valid
     * @dataProvider provide_parameters_sets
     */
    function test_is_valid($parameters, $composed_sign, $expected_validation_result)
    {
        $key = "fake-key";

        $composer = test::double(ShaComposer::class, [ 'compose' => $composed_sign ]);
        $filter   = test::double(ParametersFilter::class, [
            'filter_in_parameters' => $parameters
        ]);

        $request  = new PaymentRequest($parameters, $key);

        $validation_result = $request->is_valid();
        $this->assertEquals($expected_validation_result, $validation_result);
    }

    /**
     * @covers ::extract_sha_sign
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage SHA sign not present in parameters
     */
    function test_exception_when_extracting_sha_sign()
    {
        $extract_sha_sign_method = $this->get_method(PaymentRequest::class, 'extract_sha_sign');

        $request = new PaymentRequest($this->create_minimal_parameters(), "fake-key");
        $extract_sha_sign_method->invoke($request);
    }

    /**
     * @covers ::extract_sha_sign
     */
    function test_extract_sha_sign()
    {
        $extract_sha_sign_method = $this->get_method(PaymentRequest::class, 'extract_sha_sign');

        $expected_sign = "expected-sign";
        $parameters    = $this->create_minimal_parameters_with_sign($expected_sign);

        $request        = new PaymentRequest($parameters, "fake-key");
        $extracted_sign = $extract_sha_sign_method->invoke($request);

        $this->assertEquals($expected_sign,$extracted_sign);
    }

    /**
     * @covers ::get_parameters
     */
    function test_get_parameters()
    {
        $parameters    = $this->create_minimal_parameters();

        $request             = new PaymentRequest($parameters, "fake-key");
        $returned_parameters = $request->get_parameters();

        $this->assertEquals($parameters, $returned_parameters);
    }

    function provide_parameters_sets()
    {
        return [[
                $this->create_minimal_parameters(),
                "composed-sign",
                false
            ], [
                 $this->create_minimal_parameters_with_sign('bad-sign'),
                 "composed-sign",
                 false
            ], [
                 $this->create_minimal_parameters_with_sign('composed-sign'),
                 "composed-sign",
                 true
            ]
        ];
    }

    function create_minimal_parameters()
    {
        return [
            "SELLER"      => "test",
            "AMOUNT"      => 42.00,
            "SUCCESS_URL" => "https://balidra-payment.neto2.net/payment/test_success",
            "CANCEL_URL"  => "https://balidra-payment.neto2.net/payment/test_cancel",
            "ERROR_URL"   => "https://balidra-payment.neto2.net/payment/test_error"
        ];
    }

    function create_minimal_parameters_with_sign($sign)
    {
        $parameters = $this->create_minimal_parameters();
        $parameters[ShaComposer::SHA_SIGN_FIELD] = $sign;

        return $parameters;
    }

    function get_method($classname, $name)
    {
        $class = new ReflectionClass($classname);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}
