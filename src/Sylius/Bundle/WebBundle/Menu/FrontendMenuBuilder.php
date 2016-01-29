<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelper;
use Sylius\Bundle\WebBundle\Event\MenuBuilderEvent;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface as RbacAuthorizationCheckerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Frontend menu builder.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FrontendMenuBuilder extends MenuBuilder
{
    /**
     * Currency repository.
     *
     * @var CurrencyProviderInterface
     */
    protected $currencyProvider;

    /**
     * Taxonomy repository.
     *
     * @var RepositoryInterface
     */
    protected $taxonomyRepository;

    /**
     * Cart provider.
     *
     * @var CartProviderInterface
     */
    protected $cartProvider;

    /**
     * Currency converter helper.
     *
     * @var CurrencyHelper
     */
    protected $currencyHelper;

    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * Constructor.
     *
     * @param FactoryInterface $factory
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TranslatorInterface $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param RbacAuthorizationCheckerInterface $rbacAuthorizationChecker
     * @param CurrencyProviderInterface $currencyProvider
     * @param RepositoryInterface $taxonomyRepository
     * @param CartProviderInterface $cartProvider
     * @param CurrencyHelper $currencyHelper
     * @param ChannelContextInterface $channelContext
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        FactoryInterface $factory,
        AuthorizationCheckerInterface $authorizationChecker,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        RbacAuthorizationCheckerInterface $rbacAuthorizationChecker,
        CurrencyProviderInterface $currencyProvider,
        RepositoryInterface $taxonomyRepository,
        CartProviderInterface $cartProvider,
        CurrencyHelper $currencyHelper,
        ChannelContextInterface $channelContext,
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($factory, $authorizationChecker, $translator, $eventDispatcher, $rbacAuthorizationChecker);

        $this->currencyProvider = $currencyProvider;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->cartProvider = $cartProvider;
        $this->currencyHelper = $currencyHelper;
        $this->channelContext = $channelContext;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Builds frontend main menu.
     *
     * @return ItemInterface
     */
    public function createMainMenu()
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav nav-pills'
            )
        ));
        $menu->setCurrentUri($this->request->getRequestUri());

        if ($this->cartProvider->hasCart()) {
            $cart = $this->cartProvider->getCart();
            $cartTotals = array('items' => $cart->getTotalQuantity(), 'total' => $cart->getTotal());
        } else {
            $cartTotals = array('items' => 0, 'total' => 0);
        }

        $menu->addChild('cart', array(
            'route' => 'sylius_cart_summary',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.cart', array(
                '%items%' => $cartTotals['items'],
                '%total%' => $this->currencyHelper->convertAndFormatAmount($cartTotals['total'])
            ))),
            'labelAttributes' => array('icon' => 'icon-shopping-cart icon-large')
        ))->setLabel($this->translate('sylius.frontend.menu.main.cart', array(
            '%items%' => $cartTotals['items'],
            '%total%' => $this->currencyHelper->convertAndFormatAmount($cartTotals['total'])
        )));

        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $route = $this->request === null ? '' : $this->request->get('_route');

            if (1 === preg_match('/^(sylius_account)/', $route)) {
                $menu->addChild('shop', array(
                    'route' => 'sylius_homepage',
                    'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.shop')),
                    'labelAttributes' => array('icon' => 'icon-th icon-large', 'iconOnly' => false)
                ))->setLabel($this->translate('sylius.frontend.menu.account.shop'));
            } else {
                $menu->addChild('account', array(
                    'route' => 'sylius_account_profile_show',
                    'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.account')),
                    'labelAttributes' => array('icon' => 'icon-user icon-large', 'iconOnly' => false)
                ))->setLabel($this->translate('sylius.frontend.menu.main.account'));
            }

            $menu->addChild('logout', array(
                'route' => 'sylius_user_security_logout',
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.logout')),
                'labelAttributes' => array('icon' => 'icon-off icon-large', 'iconOnly' => false)
            ))->setLabel($this->translate('sylius.frontend.menu.main.logout'));
        } else {
            $menu->addChild('login', array(
                'route' => 'sylius_user_security_login',
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.login')),
                'labelAttributes' => array('icon' => 'icon-lock icon-large', 'iconOnly' => false)
            ))->setLabel($this->translate('sylius.frontend.menu.main.login'));
            $menu->addChild('register', array(
                'route' => 'sylius_user_registration',
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.register')),
                'labelAttributes' => array('icon' => 'icon-user icon-large', 'iconOnly' => false)
            ))->setLabel($this->translate('sylius.frontend.menu.main.register'));
        }

        if ($this->authorizationChecker->isGranted('ROLE_ADMINISTRATION_ACCESS') || $this->authorizationChecker->isGranted('ROLE_PREVIOUS_ADMIN')) {
            $routeParams = array(
                'route' => 'sylius_backend_dashboard',
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.administration')),
                'labelAttributes' => array('icon' => 'icon-briefcase icon-large', 'iconOnly' => false)
            );

            if ($this->authorizationChecker->isGranted('ROLE_PREVIOUS_ADMIN')) {
                $routeParams = array_merge($routeParams, array(
                    'route' => 'sylius_switch_user_return',
                    'routeParameters' => array(
                        'username' => $this->tokenStorage->getToken()->getUsername(),
                        '_switch_user' => '_exit'
                    )
                ));
            }

            $menu->addChild('administration', $routeParams)->setLabel($this->translate('sylius.frontend.menu.main.administration'));
        }

        $this->eventDispatcher->dispatch(MenuBuilderEvent::FRONTEND_MAIN, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    /**
     * Builds frontend currency menu.
     *
     * @return ItemInterface
     */
    public function createCurrencyMenu()
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav nav-pills'
            )
        ));

        $currencies = $this->currencyProvider->getAvailableCurrencies();
        if (1 === count($currencies)) {
            $menu->setDisplay(false);

            return $menu;
        }

        foreach ($currencies as $currency) {
            $code = $currency->getCode();

            $menu->addChild($code, array(
                'route' => 'sylius_currency_change',
                'routeParameters' => array('currency' => $code),
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.currency', array('%currency%' => $code))),
            ))->setLabel(Intl::getCurrencyBundle()->getCurrencySymbol($code));
        }

        $this->eventDispatcher->dispatch(MenuBuilderEvent::FRONTEND_CURRENCY, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    /**
     * Builds frontend taxonomies menu.
     *
     * @return ItemInterface
     */
    public function createTaxonomiesMenu()
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav'
            )
        ));
        $menu->setCurrentUri($this->request->getRequestUri());

        $childOptions = array(
            'childrenAttributes' => array('class' => 'nav nav-list'),
            'labelAttributes'    => array('class' => 'nav-header'),
        );

        $taxonomies = $this->channelContext->getChannel()->getTaxonomies();

        foreach ($taxonomies as $taxonomy) {
            $child = $menu->addChild($taxonomy->getName(), $childOptions);

            if ($taxonomy->getRoot()->hasPath()) {
                $child->setLabelAttribute('data-image', $taxonomy->getRoot()->getPath());
            }

            $this->createTaxonomiesMenuNode($child, $taxonomy->getRoot());
        }

        $this->eventDispatcher->dispatch(MenuBuilderEvent::FRONTEND_TAXONOMIES, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    /**
     * Builds frontend social menu.
     *
     * @return ItemInterface
     */
    public function createSocialMenu()
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav nav-pills pull-right'
            )
        ));

        $menu->addChild('github', array(
            'uri' => 'https://github.com/Sylius',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.social.github')),
            'labelAttributes' => array('icon' => 'icon-github-sign icon-large', 'iconOnly' => true)
        ));
        $menu->addChild('twitter', array(
            'uri' => 'https://twitter.com/Sylius',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.social.twitter')),
            'labelAttributes' => array('icon' => 'icon-twitter-sign icon-large', 'iconOnly' => true)
        ));
        $menu->addChild('facebook', array(
            'uri' => 'http://facebook.com/SyliusEcommerce',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.social.facebook')),
            'labelAttributes' => array('icon' => 'icon-facebook-sign icon-large', 'iconOnly' => true)
        ));

        $this->eventDispatcher->dispatch(MenuBuilderEvent::FRONTEND_SOCIAL, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    /**
     * Creates user account menu
     *
     * @return ItemInterface
     */
    public function createAccountMenu()
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav'
            )
        ));
        $menu->setCurrentUri($this->request->getRequestUri());

        $child = $menu->addChild($this->translate('sylius.account.title'), array(
            'childrenAttributes' => array('class' => 'nav nav-list'),
            'labelAttributes'    => array('class' => 'nav-header')
        ));

        $child->addChild('account', array(
            'route' => 'sylius_account_profile_show',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.homepage')),
            'labelAttributes' => array('icon' => 'icon-home', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.homepage'));

        $child->addChild('profile', array(
            'route' => 'sylius_account_profile_update',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.profile')),
            'labelAttributes' => array('icon' => 'icon-info-sign', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.profile'));

        $child->addChild('password', array(
            'route' => 'sylius_account_change_password',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.password')),
            'labelAttributes' => array('icon' => 'icon-lock', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.password'));

        $child->addChild('orders', array(
            'route' => 'sylius_account_order_index',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.orders')),
            'labelAttributes' => array('icon' => 'icon-briefcase', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.orders'));

        if ($this->securityContext->getToken()->getUser()->getCustomer()->getAffiliate()) {
            $child->addChild('affiliate', array(
                'route' => 'sylius_account_affiliate_index',
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.affiliate')),
                'labelAttributes' => array('icon' => 'icon-money', 'iconOnly' => false)
            ))->setLabel($this->translate('sylius.frontend.menu.account.affiliate'));
        } else {
            $child->addChild('affiliate', array(
                'route' => 'sylius_account_affiliate_signup',
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.affiliate')),
                'labelAttributes' => array('icon' => 'icon-money', 'iconOnly' => false)
            ))->setLabel($this->translate('sylius.frontend.menu.account.affiliate'));
        }


        $child->addChild('addresses', array(
            'route' => 'sylius_account_address_index',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.addresses')),
            'labelAttributes' => array('icon' => 'icon-envelope', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.addresses'));

        $this->eventDispatcher->dispatch(MenuBuilderEvent::FRONTEND_ACCOUNT, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    protected function createTaxonomiesMenuNode(ItemInterface $menu, TaxonInterface $taxon)
    {
        foreach ($taxon->getChildren() as $child) {
            $childMenu = $menu->addChild($child->getName(), array(
                'route'           => $child,
                'labelAttributes' => array('icon' => 'icon-angle-right')
            ));
            if ($child->getPath()) {
                $childMenu->setLabelAttribute('data-image', $child->getPath());
            }

            $this->createTaxonomiesMenuNode($childMenu, $child);
        }
    }
}
