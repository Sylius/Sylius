<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Security;

use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
* @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
*/
class UserLogin implements UserLoginInterface
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
     * {@inheritDoc}
     */
    public function login(UserInterface $user)
    {
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->container->get('security.context')->setToken($token);
        $this->container->get('session')->set('_security_main', serialize($token));
    }
}
