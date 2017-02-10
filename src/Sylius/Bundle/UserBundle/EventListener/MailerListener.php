<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\EventListener;

use Sylius\Bundle\UserBundle\Mailer\Emails;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class MailerListener
{
    /**
     * @var SenderInterface
     */
    private $emailSender;

    /**
     * @param SenderInterface $emailSender
     */
    public function __construct(SenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    /**
     * @param GenericEvent $event
     */
    public function sendResetPasswordTokenEmail(GenericEvent $event)
    {
        $this->sendEmail($event->getSubject(), Emails::RESET_PASSWORD_TOKEN);
    }

    /**
     * @param GenericEvent $event
     */
    public function sendResetPasswordPinEmail(GenericEvent $event)
    {
        $this->sendEmail($event->getSubject(), Emails::RESET_PASSWORD_PIN);
    }

    /**
     * @param GenericEvent $event
     */
    public function sendVerificationTokenEmail(GenericEvent $event)
    {
        $this->sendEmail($event->getSubject(), Emails::EMAIL_VERIFICATION_TOKEN);
    }

    /**
     * @param UserInterface $user
     * @param string $emailCode
     */
    private function sendEmail(UserInterface $user, $emailCode)
    {
        $this->emailSender->send($emailCode,
            [$user->getEmail()],
            [
                'user' => $user,
            ]
        );
    }
}
