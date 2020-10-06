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

use Sylius\Bundle\UserBundle\EventListener\PasswordUpdaterListener as BasePasswordUpdaterListener;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

final class CustomerEmailUpdaterListener
{
    /** @var GeneratorInterface */
    private $tokenGenerator;
    /** @var ChannelContextInterface */
    private $channelContext;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @var SessionInterface */
    private $session;

    public function __construct(
        GeneratorInterface $tokenGenerator,
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session
    ) {
        $this->tokenGenerator = $tokenGenerator;
        $this->channelContext = $channelContext;
        $this->eventDispatcher = $eventDispatcher;
        $this->session = $session;
    }

    public function eraseVerification(GenericEvent $event): void
    {
        $customer = $event->getSubject();

        /** @var CustomerInterface $customer */
        Assert::isInstanceOf($customer, CustomerInterface::class);

        /** @var ShopUserInterface $user */
        $user = $customer->getUser();
        if ($customer->getEmail() !== $user->getUsername()) {
            $user->setEmail($customer->getEmail());
            $user->setVerifiedAt(null);

            $token = $this->tokenGenerator->generate();
            $user->setEmailVerificationToken($token);

            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            if ($channel->isAccountVerificationRequired()) {
                $user->setEnabled(false);
            }
        }
    }

    public function sendVerificationEmail(GenericEvent $event): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        if (!$channel->isAccountVerificationRequired()) {
            return;
        }

        $customer = $event->getSubject();

        /** @var CustomerInterface $customer */
        Assert::isInstanceOf($customer, CustomerInterface::class);

        /** @var ShopUserInterface $user */
        $user = $customer->getUser();

        if (!$user->isEnabled() && !$user->isVerified() && null !== $user->getEmailVerificationToken()) {
            $this->eventDispatcher->dispatch(UserEvents::REQUEST_VERIFICATION_TOKEN, new GenericEvent($user));
            $this->addFlash('success', 'sylius.user.verify_email_request');
        }
    }

    private function addFlash(string $type, string $message): void
    {
        /** @var FlashBagInterface $flashBag */
        $flashBag = $this->session->getBag('flashes');
        $flashBag->add($type, $message);
    }
}
