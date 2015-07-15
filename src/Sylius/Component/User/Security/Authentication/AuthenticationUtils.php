<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\User\Security\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;

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

        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $authenticationException = $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        } elseif ($session !== null && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $authenticationException = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);

            if ($clearSession) {
                $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
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

        return null === $session ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);
    }

    /**
     * @return Request
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
