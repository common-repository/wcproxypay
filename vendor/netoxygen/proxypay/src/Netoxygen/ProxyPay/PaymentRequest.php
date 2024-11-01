<?php

namespace Netoxygen\ProxyPay;

/**
 * Represents a payment request
 *
 */
class PaymentRequest extends PaymentQuery
{
    /**
     * Constructor
     *
     * @param array $parameters: The parameters to compose the request
     *
     * @param string $key: The SHA composer secret key
     */
    public function __construct($parameters, $key)
    {
        $filter             = new ParametersFilter();
        $this->parameters   = $filter->filter_in_parameters($parameters);
        $this->sha_composer = new ShaComposer($key);
    }
}
