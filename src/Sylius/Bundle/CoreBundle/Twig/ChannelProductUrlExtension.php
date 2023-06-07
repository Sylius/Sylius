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

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\CoreBundle\Routing\Generator\ChannelProductUrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ChannelProductUrlExtension extends AbstractExtension
{
    public function __construct(
        private ChannelProductUrlGeneratorInterface $channelProductUrlGenerator,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_channel_product_url', [$this->channelProductUrlGenerator, 'generate']),
        ];
    }
}
