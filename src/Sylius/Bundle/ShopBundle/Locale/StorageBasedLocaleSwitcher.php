<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\Locale;

use Sylius\Bundle\ShopBundle\Controller\RedirectTrait;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Locale\LocaleStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

final class StorageBasedLocaleSwitcher implements LocaleSwitcherInterface
{
    use RedirectTrait;

    public function __construct(
        private LocaleStorageInterface $localeStorage,
        private ChannelContextInterface $channelContext,
        private ?RouterInterface $router = null,
    ) {
        if (null === $this->router) {
            trigger_deprecation(
                'sylius/shop-bundle',
                '2.1',
                'Not passing a "%s" to "%s" is deprecated and will be required in Sylius 3.0.',
                RouterInterface::class,
                self::class,
            );
        }
    }

    public function handle(Request $request, string $localeCode): RedirectResponse
    {
        $this->localeStorage->set($this->channelContext->getChannel(), $localeCode);

        return new RedirectResponse($this->getRedirectUrl(
            $request,
            $this->router,
            'sylius_shop_homepage',
            ['_locale' => $localeCode],
        ));
    }
}
