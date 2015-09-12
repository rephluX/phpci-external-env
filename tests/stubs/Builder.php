<?php

namespace PHPCI;

class Builder
{
    public $buildPath;

    public function log($message, $level = 'info', $context = array())
    {
    }

    public function logSuccess($message)
    {
    }

    public function logFailure($message, \Exception $exception = null)
    {
    }
}