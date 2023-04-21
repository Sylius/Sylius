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

use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantOptionsMapProvider implements ProductVariantMapProviderInterface
{
    public function provide(ProductVariantInterface $variant, array $context): array
    {
        $data = [];
        foreach ($variant->getOptionValues() as $optionValue) {
            $data[$optionValue->getOptionCode()] = $optionValue->getCode();
        }

        return $data;
    }

    public function supports(ProductVariantInterface $variant, array $context): bool
    {
        return !$variant->getOptionValues()->isEmpty();
    }
}
