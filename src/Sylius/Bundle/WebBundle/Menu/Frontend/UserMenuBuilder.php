<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Menu\Frontend;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\MenuBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Frontend user menu builder.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class UserMenuBuilder extends MenuBuilder
{
    /**
     * @var SecurityContextinterface
     */
    protected $securityContext;

    /**
     * Constructor.
     *
     * @param FactoryInterface          $factory
     * @param EventDispatcherInterface  $eventDispatcher
     * @param SecurityContextInterface  $securityContext
     */
    public function __construct(
        FactoryInterface          $factory,
        EventDispatcherInterface  $eventDispatcher,
        SecurityContextInterface  $securityContext
    )
    {
        parent::__construct($factory, $eventDispatcher);

        $this->securityContext = $securityContext;
    }

    /**
     * Build user menu.
     *
     * @return ItemInterface
     */
    public function createMenu()
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav nav-pills'
            )
        ));

        if ($this->securityContext->getToken() && $this->securityContext->isGranted('ROLE_USER')) {
            $route = $this->request === null ? '' : $this->request->get('_route');

            if (1 === preg_match('/^(sylius_account)|(fos_user)/', $route)) {
                $menu->addChild('shop', array(
                    'route' => 'sylius_homepage',
                    'linkAttributes' => array('title' => 'sylius.frontend.menu.account.shop'),
                    'labelAttributes' => array('icon' => 'icon-th icon-large', 'iconOnly' => false)
                ))->setLabel('sylius.frontend.menu.account.shop');
            } else {
                $menu->addChild('account', array(
                    'route' => 'sylius_account_homepage',
                    'linkAttributes' => array('title' => 'sylius.frontend.menu.main.account'),
                    'labelAttributes' => array('icon' => 'icon-user icon-large', 'iconOnly' => false)
                ))->setLabel('sylius.frontend.menu.main.account');
            }

            $menu->addChild('logout', array(
                'route' => 'fos_user_security_logout',
                'linkAttributes' => array('title' => 'sylius.frontend.menu.main.logout'),
                'labelAttributes' => array('icon' => 'icon-off icon-large', 'iconOnly' => false)
            ))->setLabel('sylius.frontend.menu.main.logout');
        } else {
            $menu->addChild('login', array(
                'route' => 'fos_user_security_login',
                'linkAttributes' => array('title' => 'sylius.frontend.menu.main.login'),
                'labelAttributes' => array('icon' => 'icon-lock icon-large', 'iconOnly' => false)
            ))->setLabel('sylius.frontend.menu.main.login');
            $menu->addChild('register', array(
                'route' => 'fos_user_registration_register',
                'linkAttributes' => array('title' => 'sylius.frontend.menu.main.register'),
                'labelAttributes' => array('icon' => 'icon-user icon-large', 'iconOnly' => false)
            ))->setLabel('sylius.frontend.menu.main.register');
        }

        if ($this->securityContext->getToken() && ($this->securityContext->isGranted('ROLE_SYLIUS_ADMIN') || $this->securityContext->isGranted('ROLE_PREVIOUS_ADMIN'))) {
            $routeParams = array(
                'route' => 'sylius_backend_dashboard',
                'linkAttributes' => array('title' => 'sylius.frontend.menu.main.administration'),
                'labelAttributes' => array('icon' => 'icon-briefcase icon-large', 'iconOnly' => false)
            );

            if ($this->securityContext->isGranted('ROLE_PREVIOUS_ADMIN')) {
                $routeParams = array_merge($routeParams, array(
                    'route' => 'sylius_switch_user_return',
                    'routeParameters' => array(
                        'username' => $this->securityContext->getToken()->getUsername(),
                        '_switch_user' => '_exit'
                    )
                ));
            }

            $menu->addChild('administration', $routeParams)->setLabel('sylius.frontend.menu.main.administration');
        }

        return $menu;
    }
}
