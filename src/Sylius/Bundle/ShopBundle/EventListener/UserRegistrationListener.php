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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\UserBundle\Security\UserLoginInterface;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class UserRegistrationListener
{
    /**
     * @var ObjectManager
     */
    private $userManager;

    /**
     * @var GeneratorInterface
     */
    private $tokenGenerator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var UserLoginInterface
     */
    private $userLogin;

    /**
     * @var string
     */
    private $firewallContextName;

    /**
     * @param ObjectManager $userManager
     * @param GeneratorInterface $tokenGenerator
     * @param EventDispatcherInterface $eventDispatcher
     * @param ChannelContextInterface $channelContext
     * @param UserLoginInterface $userLogin
     * @param string $firewallContextName
     */
    public function __construct(
        ObjectManager $userManager,
        GeneratorInterface $tokenGenerator,
        EventDispatcherInterface $eventDispatcher,
        ChannelContextInterface $channelContext,
        UserLoginInterface $userLogin,
        $firewallContextName
    ) {
        $this->userManager = $userManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->eventDispatcher = $eventDispatcher;
        $this->channelContext = $channelContext;
        $this->userLogin = $userLogin;
        $this->firewallContextName = $firewallContextName;
    }

    /**
     * @param GenericEvent $event
     */
    public function handleUserVerification(GenericEvent $event)
    {
        $customer = $event->getSubject();
        Assert::isInstanceOf($customer, CustomerInterface::class);

        $user = $customer->getUser();
        Assert::notNull($user);

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        if (!$channel->isAccountVerificationRequired()) {
            $this->enableAndLogin($user);
        }

        $this->sendVerificationEmail($user);
    }

    /**
     * @param ShopUserInterface $user
     */
    private function sendVerificationEmail(ShopUserInterface $user)
    {
        $token = $this->tokenGenerator->generate();
        $user->setEmailVerificationToken($token);

        $this->userManager->persist($user);
        $this->userManager->flush();

        $this->eventDispatcher->dispatch(UserEvents::REQUEST_VERIFICATION_TOKEN, new GenericEvent($user));
    }

    /**
     * @param ShopUserInterface $user
     */
    private function enableAndLogin(ShopUserInterface $user)
    {
        $user->setEnabled(true);

        $this->userLogin->login($user, $this->firewallContextName);
    }
}
