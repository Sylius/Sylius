<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

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
     * @var GeneratorInterface
     */
    protected $tokenGenerator;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param ObjectManager $userManager
     * @param GeneratorInterface $tokenGenerator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ObjectManager $userManager,
        GeneratorInterface $tokenGenerator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userManager = $userManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param GenericEvent $event
     */
    public function sendVerificationEmail(GenericEvent $event)
    {
        $customer = $event->getSubject();
        Assert::isInstanceOf($customer, CustomerInterface::class);

        $user = $customer->getUser();
        Assert::notNull($user);

        $this->handleUserVerificationToken($user);
    }

    /**
     * @param UserInterface $user
     */
    protected function handleUserVerificationToken(UserInterface $user)
    {
        $token = $this->tokenGenerator->generate();
        $user->setEmailVerificationToken($token);

        $this->userManager->persist($user);
        $this->userManager->flush();

        $this->eventDispatcher->dispatch(UserEvents::REQUEST_VERIFICATION_TOKEN, new GenericEvent($user));
    }
}
