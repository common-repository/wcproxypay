<?php

namespace Netoxygen\ProxyPay;

/**
 * Filter the incoming and outcoming query parameters
 *
 */
class ParametersFilter
{
    /**
     * Allowed fields for incomming queries
     *
     * @var array
     */
    protected $in_fields = [
        "SELLER", "AMOUNT", "DESCRIPTION", "SUCCESS_URL", "ERROR_URL", "CANCEL_URL", "CALLBACK_URL", "CALLBACK_METHOD",
        "FULLNAME", "ADDRESS", "ZIP", "CITY", "COUNTRY", "EMAIL", "ALIAS", "METHOD", "EXPM", "EXPY", ShaComposer::SHA_SIGN_FIELD
    ];

    /**
     * Allowed fields for outcoming queries
     *
     * @var array
     */
    protected $out_fields = [
        "SELLER", "AMOUNT", "TRX_ID", "STATUS", "ALIAS", "METHOD", "EXPM", "EXPY", ShaComposer::SHA_SIGN_FIELD
    ];

    /**
     * Filter the parameters with allowed $in_fields
     *
     * @param array
     *
     * @return array
     */
    public function filter_in_parameters($parameters)
    {
        $parameters = array_change_key_case($parameters, CASE_UPPER);
        return array_intersect_key($parameters, array_flip($this->in_fields));
    }

    /**
     * Filter the parameters with allowed $out_fields
     *
     * @param array
     *
     * @return array
     */
    public function filter_out_parameters($parameters)
    {
        $parameters = array_change_key_case($parameters, CASE_UPPER);
        return array_intersect_key($parameters, array_flip($this->out_fields));
    }
}
