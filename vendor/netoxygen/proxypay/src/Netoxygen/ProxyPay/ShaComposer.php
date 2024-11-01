<?php

namespace Netoxygen\ProxyPay;

/**
 * Handles the sha composing stuff
 *
 */
class ShaComposer
{
    /**
     * The SHA sign parameter name to use in
     * payment requests and responses;
     */
    const SHA_SIGN_FIELD = "SHA_SIGN";

    /**
     * The algorithm used to compose the SHA sign
     */
    const ALGORITHM = "sha256";

    /**
     * The secret key
     *
     * @var string
     */
    protected $key;

    /**
     * Constructor
     *
     * @param string $key:
     *  The secret key to use to compose the SHA sign
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Compose the SHA sign of $parameters
     *
     * @param array $parameters
     *
     * @return string
     */
    public function compose($parameters)
    {
        ksort($parameters);

        // compose SHA string
        $sha_string = '';
        foreach($parameters as $key => $value) {
            if ($key === static::SHA_SIGN_FIELD) {
                continue;
            }
            $sha_string .= $key . '=' . $value;
        }

        return hash_hmac(static::ALGORITHM, $sha_string, $this->key);
    }
}
