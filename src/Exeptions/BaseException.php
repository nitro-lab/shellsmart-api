<?php

namespace NitroLab\ShellsmartApi\Exceptions;

use Exception;
use Throwable;

class BaseException extends Exception
{
    public function __construct($message = "", $request = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
