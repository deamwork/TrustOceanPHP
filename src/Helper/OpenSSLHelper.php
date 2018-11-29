<?php
/**
 * trustocean
 * Component:
 * File: OpenSSLHelper.php
 * Author: jason
 * Time: 11/29/18 5:45 AM
 */

namespace TrustOcean\Helper;

use TrustOcean\Exception\ValidationException;

class OpenSSLHelper extends Helper
{

    /*
     * checkCSR
     * check input csr and return Common Name if CSR is intact
     *
     * @param   string  $csr
     * @throws  ValidationException
     * @return  array
     */
    public static function checkCSR(string $csr)
    {
        $csr_parse = openssl_csr_get_public_key($csr);
        if (is_null($csr_parse)) {
            throw new ValidationException('Invalid CSR');
        } else {
            return $csr_parse;
        }
    }
}
