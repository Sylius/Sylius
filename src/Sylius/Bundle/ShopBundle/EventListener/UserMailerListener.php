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
    public function sendUserRegistrationEmail(GenericEvent $event): void
    {
        $customer = $event->getSubject();

        Assert::isInstanceOf($customer, CustomerInterface::class);

        $user = $customer->getUser();
        if (null === $user) {
            return;
        }

        $email = $customer->getEmail();
        if (empty($email)) {
            return;
        }

        Assert::isInstanceOf($user, ShopUserInterface::class);

        $this->sendEmail($user, Emails::USER_REGISTRATION);
    }

    /**
     * {@inheritdoc}
     */
    protected function sendEmail(UserInterface $user, string $emailCode): void
    {
        $this->emailSender->send($emailCode, [$user->getEmail()], [
            'user' => $user,
            'channel' => $this->channelContext->getChannel(),
        ]);
    }
}
