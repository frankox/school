<?php

namespace App;
use InvalidArgumentException;


class RequiredException extends InvalidArgumentException
{
    public function __construct(string $fieldName)
    {
        parent::__construct("The field '{$fieldName}' is required.");
    }
}

