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
use Symfony\Component\HttpFoundation\Request;
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
     * @var RepositoryInterface
     */
    protected $localeProvider;

    /**
     * Constructor.
     *
     * @param FactoryInterface          $factory
     * @param SecurityContextInterface  $securityContext
     * @param TranslatorInterface       $translator
     * @param EventDispatcherInterface  $eventDispatcher
     * @param LocaleProviderInterface   $localeProvider
     * @param array                     $locales
     */
    public function __construct(
        FactoryInterface          $factory,
        SecurityContextInterface  $securityContext,
        TranslatorInterface       $translator,
        EventDispatcherInterface  $eventDispatcher,
        LocaleProviderInterface   $localeProvider,
        array                     $locales
    )
    {
        parent::__construct($factory, $securityContext, $translator, $eventDispatcher);

        $this->localeProvider = $localeProvider;
        $this->locales        = $locales;
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

        foreach ($this->locales as $locale) {
            $menu->addChild(
                $locale,
                array(
                    'route' => $this->request->attributes->get('_route'),
                    'routeParameters' => array('_locale' => $locale),
                )
            )->setLabel(Intl::getLanguageBundle()->getLanguageName($locale));
        }

        return $menu;
    }
}
