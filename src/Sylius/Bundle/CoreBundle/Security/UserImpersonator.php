<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Security;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class UserImpersonator implements UserImpersonatorInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var string
     */
    private $sessionTokenParameter;

    /**
     * @param Session $session
     * @param string $firewallContextName
     */
    public function __construct(Session $session, $firewallContextName)
    {
        $this->session = $session;
        $this->sessionTokenParameter = sprintf('_security_%s', $firewallContextName);
    }

    /**
     * {@inheritdoc}
     */
    public function impersonate(UserInterface $user)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), $this->sessionTokenParameter, $user->getRoles());

        $this->session->set($this->sessionTokenParameter, serialize($token));
        $this->session->save();
    }
}
