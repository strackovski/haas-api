<?php

namespace App\Exception\Request;

class NotFoundButRequiredException extends RequestHandlerException
{
    public function getFormat(): string
    {
        return "%s with %s %s was not found.";
    }

    public function getDefaultMessage(): string
    {
        return "The object was not found.";
    }
}
