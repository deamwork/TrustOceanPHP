<?php
/**
 * trustocean
 * Component:
 * File: ResponseException.php
 * Author: jason
 * Time: 11/29/18 1:44 AM
 */

namespace TrustOcean\Exception;

use TrustOcean\Definition\ErrorCodeDefinitions as ErrorCode;
use TrustOcean\Core\Exception;

class ResponseException extends Exception
{
    protected $code_definition;

    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        $this->code_definition = ErrorCode::define($code);
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return "[E{$this->code}]: $this->code_definition\nServer respond with: {$this->message}\n";
    }
}
