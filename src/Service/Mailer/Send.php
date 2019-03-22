<?php
namespace App\Service\Mailer;

use SidekiqJob\Client;
use Mailgun\Mailgun;
use Symfony\Bundle\TwigBundle\TwigEngine;

class Send
{
    const MAILER_WORKER = 'Mailer';

    /** @var string */
    protected $fromAddress;

    /** @var string */
    protected $fromName;

    /** @var string */
    protected $domain;

    /** @var Client */
    protected $queue;

    /** @var Mailgun */
    protected $mailgun;

    /** @var TwigEngine */
    protected $templating;

    /**
     * @param string     $fromAddress
     * @param string     $fromName
     * @param string     $domain
     * @param Client     $queue
     * @param Mailgun    $mailgun
     * @param TwigEngine $templating
     */
    public function __construct(
        string $fromAddress,
        string $fromName,
        string $domain,
        Client $queue,
        Mailgun $mailgun,
        TwigEngine $templating
    ) {
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
        $this->domain = $domain;
        $this->queue = $queue;
        $this->mailgun = $mailgun;
        $this->templating = $templating;
    }

    /**
     * @return string
     */
    public function getFromAddress()
    {
        return $this->fromAddress;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * Send email message to the message queue
     *
     * @param string $from
     * @param array $to
     * @param string $subject
     * @param string $template
     * @param array $templateArgs
     * @return string job id
     */
    public function toQueue(string $from, array $to, string $subject, string $template, array $templateArgs = [])
    {
        $payload = [[
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'template' => $template,
            'templateArgs' => $templateArgs,
        ]];
        return $this->queue->push(self::MAILER_WORKER, $payload);
    }

    /**
     * Try to send email by Mailgun
     *
     * @param string $from
     * @param array $to
     * @param string $subject
     * @param string $template
     * @param array $templateArgs
     * @return \Mailgun\Model\Message\SendResponse
     */
    public function message(string $from, array $to, string $subject, string $template, array $templateArgs = [])
    {
        $template = '@mail/' . $template;
        return $this->mailgun->messages()->send($this->domain, [
            'from'    => $from,
            'to'      => implode(',', $to),
            'subject' => $subject,
            'html'    => $this->templating->render($template, $templateArgs),
        ]);
    }
}
