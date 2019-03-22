<?php

namespace App\Service\Mailer;

use App\Entity\User;
use Aws\Exception\AwsException;
use Aws\Ses\SesClient;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class SimpleEmailService
 *
 * @package App\Service\Mailer
 * @author  Vladimir Strackovski <vlado@nv3.eu>
 */
class SimpleEmailService
{
    /** @var SesClient */
    protected $client;

    /** @var RouterInterface */
    protected $router;

    /** @var TwigEngine */
    protected $templating;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * SimpleEmailService constructor.
     *
     * @param string          $awsKey
     * @param string          $awsSecret
     * @param RouterInterface $router
     * @param TwigEngine      $templating
     * @param LoggerInterface $logger
     */
    public function __construct(
        string $awsKey,
        string $awsSecret,
        RouterInterface $router,
        TwigEngine $templating,
        LoggerInterface $logger
    ) {
        $this->client = new SesClient(
            [
                'credentials' => [
                    'key' => 'AKIAJKLDNQCMVEXFM65Q',
                    'secret' => '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',
                ],
                'version' => '2010-12-01',
                'region' => 'us-east-1',
            ]
        );
        $this->router = $router;
        $this->templating = $templating;
        $this->logger = $logger;
    }

    /**
     * Send an email with magic link to the user
     *
     * @param User $user
     *
     * @return string
     * @throws \Twig\Error\Error
     */
    public function sendMagicLink(User $user)
    {
        $sender_email = 'haas Login <info@nv3.eu>';
        $recipient_emails = [$user->getEmail()];

        $link = "https://api.staging.haas.com/mlink/".$user->getMLinkHash();
//        $link = "https://api.haas.doc/mlink/".$user->getMLinkHash();
        $html = $this->templating->render(
            "@mail/user/magic_link.html.twig",
            ["url" => $link, "username" => $user->getUsername()]
        );

        $subject = 'Your magic link is ready';
        $char_set = 'UTF-8';

        try {
            $result = $this->client->sendEmail(
                [
                    'Destination' => [
                        'ToAddresses' => $recipient_emails,
                    ],
                    'ReplyToAddresses' => [$sender_email],
                    'Source' => $sender_email,
                    'Message' => [
                        'Body' => [
                            'Html' => [
                                'Charset' => $char_set,
                                'Data' => $html,
                            ],
                        ],
                        'Subject' => [
                            'Charset' => $char_set,
                            'Data' => $subject,
                        ],
                    ],
                ]
            );
            $messageId = $result['MessageId'];
            $this->logger->critical("Email sent! Message ID: $messageId");
        } catch (AwsException $e) {
            $this->logger->critical("The email was not sent. Error message: ".$e->getAwsErrorMessage());
        }
    }
}
