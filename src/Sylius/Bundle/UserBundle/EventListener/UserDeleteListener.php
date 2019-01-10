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

namespace Sylius\Bundle\UserBundle\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

final class UserDeleteListener
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var SessionInterface */
    private $session;

    public function __construct(TokenStorageInterface $tokenStorage, SessionInterface $session)
    {
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function deleteUser(ResourceControllerEvent $event): void
    {
        $user = $event->getSubject();

        Assert::isInstanceOf($user, UserInterface::class);

        if ($this->isTryingToDeleteLoggedInAdminUser($user)) {
            $event->stopPropagation();
            $event->setErrorCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $event->setMessage('Cannot remove currently logged in user.');

            /** @var FlashBagInterface $flashBag */
            $flashBag = $this->session->getBag('flashes');
            $flashBag->add('error', 'Cannot remove currently logged in user.');
        }
    }

    private function isTryingToDeleteLoggedInAdminUser(UserInterface $user): bool
    {
        if (!$user->hasRole('ROLE_ADMINISTRATION_ACCESS') && !$user->hasRole('ROLE_API_ACCESS')) {
            return false;
        }

        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return false;
        }

        $loggedUser = $token->getUser();
        if (!$loggedUser) {
            return false;
        }

        return $loggedUser->getId() === $user->getId();
    }
}
