<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Bundle\UserBundle\EventListener\MailerListener;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class UserMailerListener extends MailerListener
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param SenderInterface $emailSender
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(SenderInterface $emailSender, ChannelContextInterface $channelContext)
    {
        parent::__construct($emailSender);

        $this->channelContext = $channelContext;
    }

    /**
     * @param GenericEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function sendUserRegistrationEmail(GenericEvent $event)
    {
        $customer = $event->getSubject();

        Assert::isInstanceOf($customer, CustomerInterface::class);

        if (null === ($user = $customer->getUser())) {
            return;
        }

        if (null === ($email = $customer->getEmail()) || empty($email)) {
            return;
        }

        /** @var ShopUserInterface $user */
        Assert::isInstanceOf($user, ShopUserInterface::class);

        $this->emailSender->send(
            Emails::USER_REGISTRATION,
            [
                $user->getEmail(),
            ],
            [
                'user' => $user,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function sendEmail($user, $emailCode)
    {
        Assert::isInstanceOf($user, UserInterface::class);

        $this->emailSender->send(
            $emailCode,
            [
                $user->getEmail(),
            ],
            [
                'user' => $user,
                'channel' => $this->channelContext->getChannel(),
            ]
        );
    }
}
