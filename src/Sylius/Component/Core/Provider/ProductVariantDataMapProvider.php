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

namespace Sylius\Component\Core\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantDataMapProvider implements ProductVariantDataMapProviderInterface
{
    /** @param ProductVariantDataMapProviderInterface[]|iterable $dataMapProviders */
    public function __construct(private iterable $dataMapProviders)
    {
    }

    public function provide(ProductVariantInterface $variant, ChannelInterface $channel): array
    {
        $data = [];

        foreach ($this->dataMapProviders as $dataMapProvider) {
            if ($dataMapProvider->supports($variant, $channel)) {
                $data += $dataMapProvider->provide($variant, $channel);
            }
        }

        return $data;
    }

    public function supports(ProductVariantInterface $variant, ChannelInterface $channel): bool
    {
        return true;
    }
}
