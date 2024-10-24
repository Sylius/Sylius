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

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\UrlHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ChannelUrlExtension extends AbstractExtension
{
    private ChannelContextInterface $channelContext;

    private UrlHelper $urlHelper;

    private bool $unsecuredUrls;

    public function __construct(
        ChannelContextInterface $channelContext,
        UrlHelper $urlHelper,
        bool $unsecuredUrls = false,
    ) {
        $this->channelContext = $channelContext;
        $this->urlHelper = $urlHelper;
        $this->unsecuredUrls = $unsecuredUrls;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_channel_url', [$this, 'generateChannelUrl']),
        ];
    }

    public function generateChannelUrl(string $path, ?ChannelInterface $channel = null): string
    {
        if (null === $channel) {
            $channel = $this->channelContext->getChannel();
        }

        if (null !== $channel->getHostname()) {
            return ($this->unsecuredUrls ? 'http://' : 'https://') . $channel->getHostname() . $path;
        }

        return $this->urlHelper->getAbsoluteUrl($path);
    }
}
