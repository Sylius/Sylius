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
use Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelperInterface;
use Sylius\Bundle\WebBundle\Event\MenuBuilderEvent;
use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface as RbacAuthorizationCheckerInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
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
     * Taxon repository.
     *
     * @var TaxonRepositoryInterface
     */
    protected $taxonRepository;

    /**
     * Cart provider.
     *
     * @var CartContextInterface
     */
    protected $cartContext;

    /**
     * Currency converter helper.
     *
     * @var CurrencyHelperInterface
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
     * @param FactoryInterface $factory
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TranslatorInterface $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param RbacAuthorizationCheckerInterface $rbacAuthorizationChecker
     * @param CurrencyProviderInterface $currencyProvider
     * @param TaxonRepositoryInterface $taxonRepository
     * @param CartContextInterface $cartContext
     * @param CurrencyHelperInterface $currencyHelper
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
        TaxonRepositoryInterface $taxonRepository,
        CartContextInterface $cartContext,
        CurrencyHelperInterface $currencyHelper,
        ChannelContextInterface $channelContext,
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($factory, $authorizationChecker, $translator, $eventDispatcher, $rbacAuthorizationChecker);

        $this->currencyProvider = $currencyProvider;
        $this->taxonRepository = $taxonRepository;
        $this->cartContext = $cartContext;
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
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'nav nav-pills',
            ],
        ]);
        $menu->setCurrentUri($this->request->getRequestUri());

        $cart = $this->cartContext->getCart();
        $cartTotals = ['items' => $cart->getTotalQuantity(), 'total' => $cart->getTotal()];

        $menu->addChild('cart', [
            'route' => 'sylius_cart_summary',
            'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.main.cart', [
                '%items%' => $cartTotals['items'],
                '%total%' => $this->currencyHelper->convertAndFormatAmount($cartTotals['total']),
            ])],
            'labelAttributes' => ['icon' => 'icon-shopping-cart icon-large'],
        ])->setLabel($this->translate('sylius.frontend.menu.main.cart', [
            '%items%' => $cartTotals['items'],
            '%total%' => $this->currencyHelper->convertAndFormatAmount($cartTotals['total']),
        ]));

        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $route = $this->request === null ? '' : $this->request->get('_route');

            if (1 === preg_match('/^(sylius_account)/', $route)) {
                $menu->addChild('shop', [
                    'route' => 'sylius_homepage',
                    'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.account.shop')],
                    'labelAttributes' => ['icon' => 'icon-th icon-large', 'iconOnly' => false],
                ])->setLabel($this->translate('sylius.frontend.menu.account.shop'));
            } else {
                $menu->addChild('account', [
                    'route' => 'sylius_account_profile_show',
                    'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.main.account')],
                    'labelAttributes' => ['icon' => 'icon-user icon-large', 'iconOnly' => false],
                ])->setLabel($this->translate('sylius.frontend.menu.main.account'));
            }

            $menu->addChild('logout', [
                'route' => 'sylius_user_security_logout',
                'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.main.logout')],
                'labelAttributes' => ['icon' => 'icon-off icon-large', 'iconOnly' => false],
            ])->setLabel($this->translate('sylius.frontend.menu.main.logout'));
        } else {
            $menu->addChild('login', [
                'route' => 'sylius_user_security_login',
                'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.main.login')],
                'labelAttributes' => ['icon' => 'icon-lock icon-large', 'iconOnly' => false],
            ])->setLabel($this->translate('sylius.frontend.menu.main.login'));
            $menu->addChild('register', [
                'route' => 'sylius_user_registration',
                'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.main.register')],
                'labelAttributes' => ['icon' => 'icon-user icon-large', 'iconOnly' => false],
            ])->setLabel($this->translate('sylius.frontend.menu.main.register'));
        }

        if ($this->authorizationChecker->isGranted('ROLE_ADMINISTRATION_ACCESS') || $this->authorizationChecker->isGranted('ROLE_PREVIOUS_ADMIN')) {
            $routeParams = [
                'route' => 'sylius_backend_dashboard',
                'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.main.administration')],
                'labelAttributes' => ['icon' => 'icon-briefcase icon-large', 'iconOnly' => false],
            ];

            if ($this->authorizationChecker->isGranted('ROLE_PREVIOUS_ADMIN')) {
                $routeParams = array_merge($routeParams, [
                    'route' => 'sylius_switch_user_return',
                    'routeParameters' => [
                        'username' => $this->tokenStorage->getToken()->getUsername(),
                        '_switch_user' => '_exit',
                    ],
                ]);
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
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'nav nav-pills',
            ],
        ]);

        $currencies = $this->currencyProvider->getAvailableCurrencies();
        if (1 === count($currencies)) {
            $menu->setDisplay(false);

            return $menu;
        }

        foreach ($currencies as $currency) {
            $code = $currency->getCode();

            $menu->addChild($code, [
                'route' => 'sylius_currency_change',
                'routeParameters' => ['currencyCode' => $code],
                'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.currency', ['%currency%' => $code])],
            ])->setLabel(Intl::getCurrencyBundle()->getCurrencySymbol($code));
        }

        $this->eventDispatcher->dispatch(MenuBuilderEvent::FRONTEND_CURRENCY, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    /**
     * Builds frontend taxons menu.
     *
     * @return ItemInterface
     */
    public function createTaxonsMenu()
    {
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'nav',
            ],
        ]);
        $menu->setCurrentUri($this->request->getRequestUri());

        $childOptions = [
            'childrenAttributes' => ['class' => 'nav nav-list'],
            'labelAttributes' => ['class' => 'nav-header'],
        ];

        $taxons = $this->channelContext->getChannel()->getTaxons();

        foreach ($taxons as $taxon) {
            $child = $menu->addChild($taxon->getName(), $childOptions);

            if ($taxon->hasPath()) {
                $child->setLabelAttribute('data-image', $taxon->getPath());
            }

            $this->createTaxonsMenuNode($child, $taxon);
        }

        $this->eventDispatcher->dispatch(MenuBuilderEvent::FRONTEND_TAXONS, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    /**
     * Builds frontend social menu.
     *
     * @return ItemInterface
     */
    public function createSocialMenu()
    {
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'nav nav-pills pull-right',
            ],
        ]);

        $menu->addChild('github', [
            'uri' => 'https://github.com/Sylius',
            'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.social.github')],
            'labelAttributes' => ['icon' => 'icon-github-sign icon-large', 'iconOnly' => true],
        ]);
        $menu->addChild('twitter', [
            'uri' => 'https://twitter.com/Sylius',
            'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.social.twitter')],
            'labelAttributes' => ['icon' => 'icon-twitter-sign icon-large', 'iconOnly' => true],
        ]);
        $menu->addChild('facebook', [
            'uri' => 'http://facebook.com/SyliusEcommerce',
            'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.social.facebook')],
            'labelAttributes' => ['icon' => 'icon-facebook-sign icon-large', 'iconOnly' => true],
        ]);

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
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'nav',
            ],
        ]);
        $menu->setCurrentUri($this->request->getRequestUri());

        $child = $menu->addChild($this->translate('sylius.account.title'), [
            'childrenAttributes' => ['class' => 'nav nav-list'],
            'labelAttributes' => ['class' => 'nav-header'],
        ]);

        $child->addChild('account', [
            'route' => 'sylius_account_profile_show',
            'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.account.homepage')],
            'labelAttributes' => ['icon' => 'icon-home', 'iconOnly' => false],
        ])->setLabel($this->translate('sylius.frontend.menu.account.homepage'));

        $child->addChild('profile', [
            'route' => 'sylius_account_profile_update',
            'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.account.profile')],
            'labelAttributes' => ['icon' => 'icon-info-sign', 'iconOnly' => false],
        ])->setLabel($this->translate('sylius.frontend.menu.account.profile'));

        $child->addChild('password', [
            'route' => 'sylius_account_change_password',
            'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.account.password')],
            'labelAttributes' => ['icon' => 'icon-lock', 'iconOnly' => false],
        ])->setLabel($this->translate('sylius.frontend.menu.account.password'));

        $child->addChild('orders', [
            'route' => 'sylius_account_order_index',
            'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.account.orders')],
            'labelAttributes' => ['icon' => 'icon-briefcase', 'iconOnly' => false],
        ])->setLabel($this->translate('sylius.frontend.menu.account.orders'));

        $child->addChild('addresses', [
            'route' => 'sylius_account_address_index',
            'linkAttributes' => ['title' => $this->translate('sylius.frontend.menu.account.addresses')],
            'labelAttributes' => ['icon' => 'icon-envelope', 'iconOnly' => false],
        ])->setLabel($this->translate('sylius.frontend.menu.account.addresses'));

        $this->eventDispatcher->dispatch(MenuBuilderEvent::FRONTEND_ACCOUNT, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     * @param TaxonInterface $taxon
     */
    protected function createTaxonsMenuNode(ItemInterface $menu, TaxonInterface $taxon)
    {
        foreach ($taxon->getChildren() as $child) {
            $childMenu = $menu->addChild($child->getName(), [
                'route' => $child,
                'labelAttributes' => ['icon' => 'icon-angle-right'],
            ]);
            if ($child->getPath()) {
                $childMenu->setLabelAttribute('data-image', $child->getPath());
            }

            $this->createTaxonsMenuNode($childMenu, $child);
        }
    }
}
