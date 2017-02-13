<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShopBundle\Locale;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Locale\LocaleStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class StorageBasedLocaleSwitcher implements LocaleSwitcherInterface
{
    /**
     * @var LocaleStorageInterface
     */
    private $localeStorage;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param LocaleStorageInterface $localeStorage
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(LocaleStorageInterface $localeStorage, ChannelContextInterface $channelContext)
    {
        $this->localeStorage = $localeStorage;
        $this->channelContext = $channelContext;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $localeCode)
    {
        $this->localeStorage->set($this->channelContext->getChannel(), $localeCode);

        return new RedirectResponse($request->headers->get('referer', $request->getSchemeAndHttpHost()));
    }
}
