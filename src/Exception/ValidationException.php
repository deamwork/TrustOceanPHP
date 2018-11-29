<?php
/**
 * trustocean
 * Component:
 * File: ValidationException.php
 * Author: jason
 * Time: 11/29/18 6:01 AM
 */

namespace TrustOcean\Exception;

class ValidationException extends \Exception
{
    protected $code_definition;

    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return "[E{$this->code}]: $this->code_definition\n{$this->message}\n";
    }
}
