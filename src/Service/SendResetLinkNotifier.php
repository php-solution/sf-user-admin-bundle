<?php

namespace PhpSolution\UserAdminBundle\Service;

use PhpSolution\UserAdminBundle\Entity\UserAdminInterface;
use Swift_Mailer as Mailer;
use Swift_Message as Message;
use Twig\Environment as Twig;

/**
 * Class SendResetLinkNotifier
 */
class SendResetLinkNotifier
{
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var string
     */
    private $senderEmail;
    /**
     * @var Twig
     */
    private $twig;
    /**
     * @var string
     */
    private $tmpl;
    /**
     * @var string
     */
    private $msgTitle;

    /**
     * SendResetLinkNotifier constructor.
     *
     * @param Mailer      $mailer
     * @param Twig        $twig
     * @param string      $tmpl
     * @param null|string $senderEmail
     * @param null|string $msgTitle
     */
    public function __construct(Mailer $mailer, Twig $twig, string $tmpl, ?string $senderEmail, ?string $msgTitle)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->tmpl = $tmpl;
        $this->senderEmail = $senderEmail;
        $this->msgTitle = $msgTitle;
    }

    /**
     * @param UserAdminInterface $userAdmin
     * @param string             $token
     */
    public function sendResetLink(UserAdminInterface $userAdmin, string $token): void
    {
        $messageText = $this->twig->render($this->tmpl, ['token' => $token, 'user_admin' => $userAdmin]);
        $message = new Message($this->msgTitle, $messageText);
        $message->setTo($userAdmin->getEmail());
        $message->setFrom($this->senderEmail);

        $this->mailer->send($message);
    }
}