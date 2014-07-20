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
use Sylius\Bundle\MoneyBundle\Twig\MoneyExtension;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\SecurityContextInterface;
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
     * @var RepositoryInterface
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
     * Money extension.
     *
     * @var MoneyExtension
     */
    protected $moneyExtension;

    /**
     * Constructor.
     *
     * @param FactoryInterface          $factory
     * @param SecurityContextInterface  $securityContext
     * @param TranslatorInterface       $translator
     * @param EventDispatcherInterface  $eventDispatcher
     * @param CurrencyProviderInterface $currencyProvider
     * @param RepositoryInterface       $taxonomyRepository
     * @param CartProviderInterface     $cartProvider
     * @param MoneyExtension            $moneyExtension
     */
    public function __construct(
        FactoryInterface          $factory,
        SecurityContextInterface  $securityContext,
        TranslatorInterface       $translator,
        EventDispatcherInterface  $eventDispatcher,
        CurrencyProviderInterface $currencyProvider,
        RepositoryInterface       $taxonomyRepository,
        CartProviderInterface     $cartProvider,
        MoneyExtension            $moneyExtension
    )
    {
        parent::__construct($factory, $securityContext, $translator, $eventDispatcher);

        $this->currencyProvider = $currencyProvider;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->cartProvider = $cartProvider;
        $this->moneyExtension = $moneyExtension;
    }

    /**
     * Builds frontend main menu.
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createMainMenu(Request $request)
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav nav-pills'
            )
        ));

        if ($this->cartProvider->hasCart()) {
            $cart = $this->cartProvider->getCart();
            $cartTotals = array('items' => $cart->countItems(), 'total' => $cart->getTotal());
        } else {
            $cartTotals = array('items' => 0, 'total' => 0);
        }

        $menu->addChild('cart', array(
            'route' => 'sylius_cart_summary',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.cart', array(
                '%items%' => $cartTotals['items'],
                '%total%' => $this->moneyExtension->formatAmount($cartTotals['total'])
            ))),
            'labelAttributes' => array('icon' => 'icon-shopping-cart icon-large')
        ))->setLabel($this->translate('sylius.frontend.menu.main.cart', array(
            '%items%' => $cartTotals['items'],
            '%total%' => $this->moneyExtension->formatAmount($cartTotals['total'])
        )));

        if ($this->securityContext->getToken() && $this->securityContext->isGranted('ROLE_USER')) {
            $route = $this->request === null ? '' : $this->request->get('_route');

            if (1 === preg_match('/^(sylius_account)|(fos_user)/', $route)) {
                $menu->addChild('shop', array(
                    'route' => 'sylius_homepage',
                    'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.shop')),
                    'labelAttributes' => array('icon' => 'icon-th icon-large', 'iconOnly' => false)
                ))->setLabel($this->translate('sylius.frontend.menu.account.shop'));
            } else {
                $menu->addChild('account', array(
                    'route' => 'sylius_account_homepage',
                    'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.account')),
                    'labelAttributes' => array('icon' => 'icon-user icon-large', 'iconOnly' => false)
                ))->setLabel($this->translate('sylius.frontend.menu.main.account'));
            }

            $menu->addChild('logout', array(
                'route' => 'fos_user_security_logout',
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.logout')),
                'labelAttributes' => array('icon' => 'icon-off icon-large', 'iconOnly' => false)
            ))->setLabel($this->translate('sylius.frontend.menu.main.logout'));
        } else {
            $menu->addChild('login', array(
                'route' => 'fos_user_security_login',
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.login')),
                'labelAttributes' => array('icon' => 'icon-lock icon-large', 'iconOnly' => false)
            ))->setLabel($this->translate('sylius.frontend.menu.main.login'));
            $menu->addChild('register', array(
                'route' => 'fos_user_registration_register',
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.register')),
                'labelAttributes' => array('icon' => 'icon-user icon-large', 'iconOnly' => false)
            ))->setLabel($this->translate('sylius.frontend.menu.main.register'));
        }

        if ($this->securityContext->getToken() && ($this->securityContext->isGranted('ROLE_SYLIUS_ADMIN') || $this->securityContext->isGranted('ROLE_PREVIOUS_ADMIN'))) {
            $routeParams = array(
                'route' => 'sylius_backend_dashboard',
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.administration')),
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

            $menu->addChild('administration', $routeParams)->setLabel($this->translate('sylius.frontend.menu.main.administration'));
        }

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

        foreach ($this->currencyProvider->getAvailableCurrencies() as $currency) {
            $code = $currency->getCode();

            $menu->addChild($code, array(
                'route' => 'sylius_currency_change',
                'routeParameters' => array('currency' => $code),
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.currency', array('%currency%' => $code))),
            ))->setLabel(Intl::getCurrencyBundle()->getCurrencySymbol($code));
        }

        return $menu;
    }

    /**
     * Builds frontend taxonomies menu.
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createTaxonomiesMenu(Request $request)
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav'
            )
        ));

        $childOptions = array(
            'childrenAttributes' => array('class' => 'nav nav-list'),
            'labelAttributes'    => array('class' => 'nav-header'),
        );

        $taxonomies = $this->taxonomyRepository->findAll();

        foreach ($taxonomies as $taxonomy) {
            $child = $menu->addChild($taxonomy->getName(), $childOptions);

            if ($taxonomy->getRoot()->hasPath()) {
                $child->setLabelAttribute('data-image', $taxonomy->getRoot()->getPath());
            }

            $this->createTaxonomiesMenuNode($child, $taxonomy->getRoot());
        }

        return $menu;
    }

    private function createTaxonomiesMenuNode(ItemInterface $menu, TaxonInterface $taxon)
    {
        foreach ($taxon->getChildren() as $child) {
            $childMenu = $menu->addChild($child->getName(), array(
                'route'           => 'sylius_product_index_by_taxon',
                'routeParameters' => array('permalink' => $child->getPermalink()),
                'labelAttributes' => array('icon' => 'icon-angle-right')
            ));
            if ($child->getPath()) {
                $childMenu->setLabelAttribute('data-image', $child->getPath());
            }

            $this->createTaxonomiesMenuNode($childMenu, $child);
        }
    }

    /**
     * Builds frontend social menu.
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createSocialMenu(Request $request)
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

        return $menu;
    }

    /**
     * Creates user account menu
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createAccountMenu(Request $request)
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav'
            )
        ));

        $childOptions = array(
            'childrenAttributes' => array('class' => 'nav nav-list'),
            'labelAttributes'    => array('class' => 'nav-header')
        );

        $child = $menu->addChild($this->translate('sylius.account.title'), $childOptions);

        $child->addChild('account', array(
            'route' => 'sylius_account_homepage',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.homepage')),
            'labelAttributes' => array('icon' => 'icon-home', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.homepage'));

        $child->addChild('profile', array(
            'route' => 'fos_user_profile_edit',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.profile')),
            'labelAttributes' => array('icon' => 'icon-info-sign', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.profile'));

        $child->addChild('password', array(
            'route' => 'fos_user_change_password',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.password')),
            'labelAttributes' => array('icon' => 'icon-lock', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.password'));

        $child->addChild('orders', array(
            'route' => 'sylius_account_order_index',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.orders')),
            'labelAttributes' => array('icon' => 'icon-briefcase', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.orders'));

        $child->addChild('addresses', array(
            'route' => 'sylius_account_address_index',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.addresses')),
            'labelAttributes' => array('icon' => 'icon-envelope', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.addresses'));

        return $menu;
    }
}
