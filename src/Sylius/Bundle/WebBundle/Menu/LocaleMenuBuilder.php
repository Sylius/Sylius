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
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;

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
     * @var LocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * Constructor.
     *
     * @param FactoryInterface         $factory
     * @param SecurityContextInterface $securityContext
     * @param TranslatorInterface      $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param LocaleProviderInterface  $localeProvider
     */
    public function __construct(
        FactoryInterface          $factory,
        SecurityContextInterface  $securityContext,
        TranslatorInterface       $translator,
        EventDispatcherInterface  $eventDispatcher,
        LocaleProviderInterface   $localeProvider
    ) {
        parent::__construct($factory, $securityContext, $translator, $eventDispatcher);

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
