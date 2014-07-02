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
use Sylius\Bundle\UiBundle\Menu\MenuBuilder;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Intl\Intl;

/**
 * Menu builder for selecting currencies.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyMenuBuilder extends MenuBuilder
{
    /**
     * Currency repository.
     *
     * @var RepositoryInterface
     */
    protected $currencyProvider;

    /**
     * Constructor.
     *
     * @param FactoryInterface          $factory
     * @param EventDispatcherInterface  $eventDispatcher
     * @param CurrencyProviderInterface $currencyProvider
     */
    public function __construct(
        FactoryInterface            $factory,
        EventDispatcherInterface    $eventDispatcher,
        CurrencyProviderInterface   $currencyProvider
    )
    {
        parent::__construct($factory, $eventDispatcher);

        $this->currencyProvider = $currencyProvider;
    }

    /**
     * Builds frontend currency menu.
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

        foreach ($this->currencyProvider->getAvailableCurrencies() as $currency) {
            $code = $currency->getCode();

            $menu->addChild($code, array(
                'route' => 'sylius_currency_change',
                'routeParameters' => array('currency' => $code),
            ))->setLabel(Intl::getCurrencyBundle()->getCurrencySymbol($code));
        }

        return $menu;
    }
}
