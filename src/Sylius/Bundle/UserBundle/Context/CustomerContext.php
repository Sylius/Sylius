<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Context;

use Sylius\Component\User\Context\CustomerContextInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerContext implements CustomerContextInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param SessionInterface              $session
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        SessionInterface $session
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->session = $session;
    }

    /**
     * Gets customer based on currently logged user or from session (if user is not authenticated).
     *
     * @return CustomerInterface|null
     */
    public function getCustomer()
    {
        $user = $this->getUser();

        if (null !== $user && $user instanceof UserInterface) {
            return $user->getCustomer();
        }

        return $this->session->get('customer');
    }

    /**
     * Stores customer in session.
     *
     * @param CustomerInterface $customer
     */
    public function setCustomer(CustomerInterface $customer)
    {
        $this->session->set('customer', $customer);
    }

    /**
     * @return UserInterface|null
     */
    protected function getUser()
    {
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            if ($token = $this->tokenStorage->getToken()) {
                return $token->getUser();
            }
        }

        return null;
    }
}
