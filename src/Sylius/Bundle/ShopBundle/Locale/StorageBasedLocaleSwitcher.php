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

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Locale\LocaleStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

final class StorageBasedLocaleSwitcher implements LocaleSwitcherInterface
{
    public function __construct(
        private LocaleStorageInterface $localeStorage,
        private ChannelContextInterface $channelContext,
        private ?UrlMatcherInterface $urlMatcher = null,
    ) {
        if (null === $this->urlMatcher) {
            trigger_deprecation(
                'sylius/shop-bundle',
                '2.1',
                'Not passing a "%s" to "%s" is deprecated and will be required in Sylius 3.0.',
                UrlMatcherInterface::class,
                self::class,
            );
        }
    }

    public function handle(Request $request, string $localeCode): RedirectResponse
    {
        $this->localeStorage->set($this->channelContext->getChannel(), $localeCode);
        $url = $request->headers->get('referer', $request->getSchemeAndHttpHost());

        if ($this->urlMatcher) {
            try {
                $this->urlMatcher->match($url);
            } catch (ResourceNotFoundException) {
                return new RedirectResponse($request->getSchemeAndHttpHost());
            }
        }

        return new RedirectResponse($url);
    }
}
