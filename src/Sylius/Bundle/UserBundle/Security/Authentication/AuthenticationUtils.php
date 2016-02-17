<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Security\Authentication;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

/**
 * Extracts Security Errors from Request
 *
 * @author Boris Vujicic <boris.vujicic@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class AuthenticationUtils
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param bool $clearSession
     *
     * @return AuthenticationException|null
     */
    public function getLastAuthenticationError($clearSession = true)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $authenticationException = null;

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $authenticationException = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } elseif ($session !== null && $session->has(Security::AUTHENTICATION_ERROR)) {
            $authenticationException = $session->get(Security::AUTHENTICATION_ERROR);

            if ($clearSession) {
                $session->remove(Security::AUTHENTICATION_ERROR);
            }
        }

        return $authenticationException;
    }

    /**
     * @return string
     */
    public function getLastUsername()
    {
        $session = $this->getRequest()->getSession();

        return null === $session ? '' : $session->get(Security::LAST_USERNAME);
    }

    /**
     * @return Request
     *
     * @throws \LogicException
     */
    private function getRequest()
    {
        $request = $this->container->get('request');

        if (null === $request) {
            throw new \LogicException('Request should exist so it can be processed for error.');
        }

        return $request;
    }
}
