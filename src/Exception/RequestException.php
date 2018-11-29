<?php
/**
 * trustocean
 * Component:
 * File: RequestException.php
 * Author: jason
 * Time: 11/29/18 1:44 AM
 */

namespace TrustOcean\Exception;

class RequestException extends \Exception
{
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__.": [{$this->code}]: {$this->message}n";
    }

    public function setToDefault()
    {
    }
}
