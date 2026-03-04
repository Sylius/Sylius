<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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

    /** @var LocaleStorageInterface */
    private $localeStorage;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var RouterInterface|null */
    private $router;

    public function __construct(LocaleStorageInterface $localeStorage, ChannelContextInterface $channelContext, ?RouterInterface $router = null)
    {
        $this->localeStorage = $localeStorage;
        $this->channelContext = $channelContext;
        $this->router = $router;
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
