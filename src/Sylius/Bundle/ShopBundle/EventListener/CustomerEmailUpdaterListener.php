<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

final readonly class CustomerEmailUpdaterListener
{
    public function __construct(
        private GeneratorInterface $tokenGenerator,
        private ChannelContextInterface $channelContext,
        private EventDispatcherInterface $eventDispatcher,
        private RequestStack $requestStack,
        private SectionProviderInterface $uriBasedSectionContext,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function eraseVerification(GenericEvent $event): void
    {
        if (!$this->uriBasedSectionContext->getSection() instanceof ShopSection) {
            return;
        }

        $customer = $event->getSubject();

        /** @var CustomerInterface $customer */
        Assert::isInstanceOf($customer, CustomerInterface::class);

        /** @var ShopUserInterface|null $user */
        $user = $customer->getUser();
        Assert::isInstanceOf($user, ShopUserInterface::class);
        Assert::methodExists($user, 'getUsername');

        if ($customer->getEmail() !== $user->getUsername()) {
            $user->setVerifiedAt(null);

            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            if ($channel->isAccountVerificationRequired()) {
                $token = $this->tokenGenerator->generate();
                $user->setEmailVerificationToken($token);

                $user->setEnabled(false);

                $this->tokenStorage->setToken(null);
            }
        }
    }

    public function sendVerificationEmail(GenericEvent $event): void
    {
        if (!$this->uriBasedSectionContext->getSection() instanceof ShopSection) {
            return;
        }

        $customer = $event->getSubject();

        /** @var CustomerInterface $customer */
        Assert::isInstanceOf($customer, CustomerInterface::class);

        /** @var ShopUserInterface $user */
        $user = $customer->getUser();
        Assert::isInstanceOf($user, ShopUserInterface::class);

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        if (!$channel->isAccountVerificationRequired()) {
            return;
        }

        if (!$user->isEnabled() && !$user->isVerified() && null !== $user->getEmailVerificationToken()) {
            $this->eventDispatcher->dispatch(new GenericEvent($user), UserEvents::REQUEST_VERIFICATION_TOKEN);

            $flashBag = FlashBagProvider::getFlashBag($this->requestStack);
            $flashBag->add('success', 'sylius.user.verify_email_request');
        }
    }
}
