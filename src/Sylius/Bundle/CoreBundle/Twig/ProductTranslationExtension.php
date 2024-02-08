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

use Sylius\Bundle\CoreBundle\Provider\ChannelBasedProductTranslationProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ProductTranslationExtension extends AbstractExtension
{
    public function __construct(
        private ChannelBasedProductTranslationProviderInterface $channelBasedProductTranslationProvider,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_product_translation', [$this->channelBasedProductTranslationProvider, 'provide']),
        ];
    }
}
