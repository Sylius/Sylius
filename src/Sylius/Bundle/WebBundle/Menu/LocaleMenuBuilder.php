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
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Intl\Intl;

/**
 * Menu builder for selecting locales.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LocaleMenuBuilder extends MenuBuilder
{
    /**
     * Locale repository.
     *
     * @var RepositoryInterface
     */
    protected $localeProvider;

    /**
     * Constructor.
     *
     * @param FactoryInterface          $factory
     * @param EventDispatcherInterface  $eventDispatcher
     * @param LocaleProviderInterface   $localeProvider
     */
    public function __construct(
        FactoryInterface         $factory,
        EventDispatcherInterface $eventDispatcher,
        LocaleProviderInterface  $localeProvider
    )
    {
        parent::__construct($factory, $eventDispatcher);

        $this->localeProvider = $localeProvider;
    }

    /**
     * Builds frontend locale menu.
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

        foreach ($this->localeProvider->getAvailableLocales() as $locale) {
            $code = $locale->getCode();

            $menu->addChild($code, array(
                'route' => 'sylius_locale_change',
                'routeParameters' => array('locale' => $code),
            ))->setLabel(Intl::getLocaleBundle()->getLocaleName($code));
        }

        return $menu;
    }
}
