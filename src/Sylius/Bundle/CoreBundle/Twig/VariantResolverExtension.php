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

use Sylius\Bundle\CoreBundle\Templating\Helper\VariantResolverHelper;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class VariantResolverExtension extends AbstractExtension
{
    public function __construct(private readonly VariantResolverHelper|ProductVariantResolverInterface $helper)
    {
        if ($this->helper instanceof VariantResolverHelper) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.14',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be prohibited in Sylius 2.0. Pass an instance of %s instead.',
                VariantResolverHelper::class,
                self::class,
                ProductVariantResolverInterface::class,
            );

            trigger_deprecation(
                'sylius/core-bundle',
                '1.14',
                'The argument name $helper is deprecated and will be renamed to $productVariantResolver in Sylius 2.0.',
            );
        }
    }

    public function getFilters(): array
    {
        if ($this->helper instanceof ProductVariantResolverInterface) {
            return [
                new TwigFilter('sylius_resolve_variant', [$this->helper, 'getVariant']),
            ];
        }

        return [
            new TwigFilter('sylius_resolve_variant', [$this->helper, 'resolveVariant']),
        ];
    }
}
