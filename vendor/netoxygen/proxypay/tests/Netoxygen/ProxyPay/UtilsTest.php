<?php

use Netoxygen\ProxyPay\Utils;

/**
 * Tests the utils class
 *
 * @coversDefaultClass Netoxygen\ProxyPay\Utils
 */
class UtilsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netoxygen\ProxyPay\Utils::hash_equals
     * @dataProvider provide_invalid_arguments
     */
    function test_invalid_arguments($first_argument, $second_argument)
    {
        $this->setExpectedException(InvalidArgumentException::class);
        Utils::hash_equals($first_argument, $second_argument);
    }

    /**
     * @covers \Netoxygen\ProxyPay\Utils::hash_equals
     * @dataProvider provide_diff_length_arguments
     */
    function test_diff_length_arguments($first_argument, $second_argument)
    {
        $result = Utils::hash_equals($first_argument, $second_argument);
        $this->assertFalse($result);
    }

    /**
     * @covers \Netoxygen\ProxyPay\Utils::hash_equals
     */
    function test_different_arguments_of_same_length()
    {
        $result = Utils::hash_equals("valid_string_1", "valid_string_2");
        $this->assertFalse($result);
    }

    /**
     * @covers \Netoxygen\ProxyPay\Utils::hash_equals
     */
    function test_same_arguments()
    {
        $result = Utils::hash_equals("valid_string", "valid_string");
        $this->assertTrue($result);
    }

    function provide_invalid_arguments() {
        $valid_argument = "Valid argument";
        return [
            [null, $valid_argument],
            [$valid_argument, null],

            [42, $valid_argument],
            [$valid_argument, 42],

            [new stdClass(), $valid_argument],
            [$valid_argument, new stdClass()],

            [[], $valid_argument],
            [$valid_argument, []]
        ];
    }

    function provide_diff_length_arguments() {
        return [
            ["short", "longer"],
            ["longer", "short"],
            ["", "valid"],
            ["valid", ""]
        ];
    }
}
