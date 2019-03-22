<?php

namespace App\Exception\Request;



class MultipleAssociationException extends RequestHandlerException
{
    public function getFormat(): string
    {
        return "%s %s already exists in %s %s.";
    }

    public function getDefaultMessage(): string
    {
        return "This association already exists.";
    }
}
