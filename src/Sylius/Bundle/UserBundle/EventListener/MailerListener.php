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
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Mailer listener for User actions
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class MailerListener
{
    /**
     * @var SenderInterface
     */
    protected $emailSender;

    public function __construct(SenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    public function sendPasswordResetEmail(GenericEvent $event)
    {
        $user = $event->getSubject();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user,
                'Sylius\Component\User\Model\UserInterface'
            );
        }

        $this->emailSender->send(Emails::PASSWORD_RESET,
            array($user->getEmail()),
                array(
                    'user' => $user,
                )
            );
    }
}
