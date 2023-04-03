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

namespace Sylius\Component\Core\Provider\ProductVariantMap;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface ProductVariantMapProviderInterface
{
    public function provide(ProductVariantInterface $variant, ChannelInterface $channel): array;

    public function supports(ProductVariantInterface $variant, ChannelInterface $channel): bool;
}
