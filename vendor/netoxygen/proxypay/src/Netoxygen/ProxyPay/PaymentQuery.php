<?php

namespace Netoxygen\ProxyPay;

/**
 * Represents an abstract payment query
 *
 * @see PaymentRequest and PaymentResponse
 */
abstract class PaymentQuery
{
    /**
     * The payment query parameters
     *
     * @var array
     */
    protected $parameters;

    /**
     * The SHA composer to use to generate the SHA sign
     *
     * @var ShaComposer
     */
    protected $sha_composer;

    /**
     * Compose the SHA sign of this payment query
     *
     * @return string
     */
    public function compose_sha_sign()
    {
        return $this->sha_composer->compose($this->parameters);
    }

    /**
     * Check if this query is correctly signed
     *
     * @return string
     */
    public function is_valid()
    {
        try {
            $sha_sign = $this->extract_sha_sign();
        } catch(\InvalidArgumentException $e) {
            return false;
        }

        return Utils::hash_equals($sha_sign, $this->compose_sha_sign());
    }

    /**
     * Extract the SHA sign from the query parameters
     *
     * @throws InvalidArgumentException if the SHA sign
     * is not found in the response parameters
     *
     * @return string
     */
    protected function extract_sha_sign()
    {
        if (!array_key_exists(ShaComposer::SHA_SIGN_FIELD, $this->parameters)) {
            throw new \InvalidArgumentException("SHA sign not present in parameters");
        }

        return $this->parameters[ShaComposer::SHA_SIGN_FIELD];
    }

    /**
     * Parameters getter
     *
     * @return array: This query parameters
     */
    public function get_parameters()
    {
        return $this->parameters;
    }
}

