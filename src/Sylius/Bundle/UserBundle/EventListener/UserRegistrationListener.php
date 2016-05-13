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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\TokenProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class UserRegistrationListener
{
    /**
     * @var ObjectManager
     */
    protected $userManager;

    /**
     * @var TokenProviderInterface
     */
    protected $tokenProvider;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param ObjectManager $userManager
     * @param TokenProviderInterface $tokenProvider
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ObjectManager $userManager,
        TokenProviderInterface $tokenProvider,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userManager = $userManager;
        $this->tokenProvider = $tokenProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param GenericEvent $event
     */
    public function sendVerificationEmail(GenericEvent $event)
    {
        $customer = $event->getSubject();
        if (!$customer instanceof CustomerInterface) {
            throw new UnexpectedTypeException(
                $customer,
                CustomerInterface::class
            );
        }
        if (null === $user = $customer->getUser()) {
            return;
        }
        if (!$user->isEnabled()) {
            return;
        }

        $this->handleUserVerificationToken($user);
    }

    /**
     * @param UserInterface $user
     */
    protected function handleUserVerificationToken(UserInterface $user)
    {
        $token = $this->tokenProvider->generateUniqueToken();
        $user->setEmailVerificationToken($token);

        $this->userManager->persist($user);
        $this->userManager->flush();

        $this->eventDispatcher->dispatch(UserEvents::REQUEST_VERIFICATION_TOKEN, new GenericEvent($user));
    }
}
