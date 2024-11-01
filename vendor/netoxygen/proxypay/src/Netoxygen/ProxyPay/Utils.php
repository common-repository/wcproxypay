<?php

namespace Netoxygen\ProxyPay;

/**
 * Provide utility functions
 *
 */
class Utils
{
    public static function hash_equals($known_string, $user_string)
    {
        if (!is_string($known_string)) {
            throw new \InvalidArgumentException(sprintf('Expected string, got %s', gettype($known_string)));
        }

        if (!is_string($user_string)) {
            throw new \InvalidArgumentException(sprintf('Expected string, got %s', gettype($user_string)));
        }

        $known_length = strlen($known_string);
        if ($known_length !== strlen($user_string)) {
            return false;
        }

        $diff = 0;
        for ($i = 0; $i < $known_length; $i++) {
            $diff |= (ord($known_string[$i]) ^ ord($user_string[$i]));
        }

        return 0 === $diff;
    }
}
