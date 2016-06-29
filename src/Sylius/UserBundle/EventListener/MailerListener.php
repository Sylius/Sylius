<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\UserBundle\EventListener;

use Sylius\UserBundle\Mailer\Emails;
use Sylius\Mailer\Sender\SenderInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Sylius\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Mailer listener for User actions.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class MailerListener
{
    /**
     * @var SenderInterface
     */
    protected $emailSender;

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
     * @param mixed  $user
     * @param string $emailCode
     */
    protected function sendEmail($user, $emailCode)
    {
        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user,
                UserInterface::class
            );
        }

        $this->emailSender->send($emailCode,
            [$user->getEmail()],
            [
                'user' => $user,
            ]
        );
    }
}
