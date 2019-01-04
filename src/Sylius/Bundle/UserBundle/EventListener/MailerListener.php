<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UserBundle\EventListener;

use Sylius\Bundle\UserBundle\Mailer\Emails;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class MailerListener
{
    /** @var SenderInterface */
    protected $emailSender;

    public function __construct(SenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    public function sendResetPasswordTokenEmail(GenericEvent $event): void
    {
        $this->sendEmail($event->getSubject(), Emails::RESET_PASSWORD_TOKEN);
    }

    public function sendResetPasswordPinEmail(GenericEvent $event): void
    {
        $this->sendEmail($event->getSubject(), Emails::RESET_PASSWORD_PIN);
    }

    public function sendVerificationTokenEmail(GenericEvent $event): void
    {
        $this->sendEmail($event->getSubject(), Emails::EMAIL_VERIFICATION_TOKEN);
    }

    protected function sendEmail(UserInterface $user, string $emailCode): void
    {
        $this->emailSender->send($emailCode, [$user->getEmail()], ['user' => $user]);
    }
}
