<?php
namespace App\Service\Mailer;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use Symfony\Component\Routing\RouterInterface;
use App\Service\Mailer\Send;

class FOSMailer implements MailerInterface
{
    /** @var Send */
    protected $send;

    /** @var RouterInterface */
    protected $router;

    public function __construct(Send $send, RouterInterface $router)
    {
        $this->send = $send;
        $this->router = $router;
    }

    /**
     * Send an email to a user to confirm the account creation
     *
     * @param UserInterface $user
     *
     * @return string
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        return $this->sendToQueue(
            $user,
            'Activate your NV_CFG__PROJECT_TITLE account',
            'user/confirmation.html.twig',
            [
                'url' => $this->router->generate(
                    'fos_user_registration_confirm',
                    ['token' => $user->getConfirmationToken()],
                    RouterInterface::ABSOLUTE_URL
                ),
            ]
        );
    }

    /**
     * Send an email to a user to confirm the password reset
     *
     * @param UserInterface $user
     *
     * @return string
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        return $this->sendToQueue(
            $user,
            'Reset your haas account email',
            'user/reset_password.html.twig',
            [
                'url' => $this->router->generate(
                    'fos_user_resetting_reset',
                    ['token' => $user->getConfirmationToken()],
                    RouterInterface::ABSOLUTE_URL
                ),
            ]
        );
    }

    /**
     * Send email message to the message queue
     *
     * @param UserInterface $user
     * @param string $subject
     * @param string $template
     * @param array $templateArgs
     * @return string
     */
    private function sendToQueue(UserInterface $user, string $subject, string $template, array $templateArgs = [])
    {
        $templateArgs = array_merge(
            [
                'username' => $user->getUsername(),
            ],
            $templateArgs
        );
        return $this->send->toQueue(
            $this->send->getFromAddress(),
            [$user->getEmail()],
            $subject,
            $template,
            $templateArgs
        );
    }
}
