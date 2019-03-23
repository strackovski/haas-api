<?php

namespace App\Exception\Request;

use Throwable;

abstract class RequestHandlerException extends \Exception
{
    protected $guruId;

    public function __construct(array $messageArgs, $code = 100, Throwable $previous = null)
    {
        $args = [];

        foreach ($messageArgs as $messageArg) {
            if (strpos($messageArg, "\\") !== false) {
                $args[] = substr($messageArg, strrpos($messageArg, "\\") + 1);
            } else {
                $args[] = $messageArg;
            }
        }

        $message = (substr_count($this->getFormat(), "%s") === count($args)) ? vsprintf(
            $this->getFormat(),
            $args
        ) : $this->getDefaultMessage();

        parent::__construct($message, $code, $previous);
        $this->guruId = uniqid(time());
    }

    abstract public function getFormat(): string;

    abstract public function getDefaultMessage(): string;

    public function getGuruId()
    {
        return $this->guruId;
    }
}
