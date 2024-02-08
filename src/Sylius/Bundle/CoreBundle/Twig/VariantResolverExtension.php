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
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class VariantResolverExtension extends AbstractExtension
{
    public function __construct(private VariantResolverHelper $helper)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sylius_resolve_variant', [$this->helper, 'resolveVariant']),
        ];
    }
}
