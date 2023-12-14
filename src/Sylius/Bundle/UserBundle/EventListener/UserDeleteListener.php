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

namespace Sylius\Bundle\UserBundle\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Webmozart\Assert\Assert;

final class UserDeleteListener
{
    public function __construct(private TokenStorageInterface $tokenStorage, private RequestStack|SessionInterface $requestStackOrSession)
    {
        if ($requestStackOrSession instanceof SessionInterface) {
            trigger_deprecation(
                'sylius/user-bundle',
                '1.12',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be removed in 2.0. Pass an instance of %s instead.',
                SessionInterface::class,
                self::class,
                RequestStack::class,
            );
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function deleteUser(ResourceControllerEvent $event): void
    {
        $user = $event->getSubject();

        Assert::isInstanceOf($user, UserInterface::class);

        if ($this->isTryingToDeleteLoggedInUser($user)) {
            $event->stopPropagation();
            $event->setErrorCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $event->setMessage('Cannot remove currently logged in user.');

            if ($this->requestStackOrSession instanceof SessionInterface) {
                $session = $this->requestStackOrSession;
            } else {
                $session = $this->requestStackOrSession->getSession();
            }

            /** @var FlashBagInterface $flashBag */
            $flashBag = $session->getBag('flashes');
            $flashBag->add('error', 'Cannot remove currently logged in user.');
        }
    }

    private function isTryingToDeleteLoggedInUser(UserInterface $user): bool
    {
        Assert::isInstanceOf($user, ResourceInterface::class);
        Assert::isInstanceOf($user, SymfonyUserInterface::class);
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return false;
        }

        $loggedUser = $token->getUser();
        if ($loggedUser === null) {
            return false;
        }
        Assert::isInstanceOf($loggedUser, ResourceInterface::class);

        return $loggedUser->getId() === $user->getId() && $loggedUser->getRoles() === $user->getRoles();
    }
}
