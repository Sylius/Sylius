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
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface as RbacAuthorizationCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Menu builder for selecting locales.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LocaleMenuBuilder extends MenuBuilder
{
    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * @param FactoryInterface $factory
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TranslatorInterface $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param LocaleProviderInterface $localeProvider
     * @param RbacAuthorizationCheckerInterface $rbacAuthorizationChecker
     */
    public function __construct(
        FactoryInterface $factory,
        AuthorizationCheckerInterface $authorizationChecker,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        LocaleProviderInterface $localeProvider,
        RbacAuthorizationCheckerInterface $rbacAuthorizationChecker
    ) {
        parent::__construct($factory, $authorizationChecker, $translator, $eventDispatcher, $rbacAuthorizationChecker);

        $this->localeProvider = $localeProvider;
    }

    /**
     * Builds frontend locale menu.
     *
     * @return ItemInterface
     */
    public function createMenu()
    {
        $locales = $this->localeProvider->getAvailableLocales();
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'nav nav-pills',
            ],
        ]);

        if (1 === count($locales)) {
            $menu->setDisplay(false);

            return $menu;
        }

        foreach ($locales as $locale) {
            $menu->addChild($locale, [
                'route' => 'sylius_locale_change',
                'routeParameters' => ['locale' => $locale],
            ])->setLabel(Intl::getLocaleBundle()->getLocaleName($locale));
        }

        return $menu;
    }
}
