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

namespace Sylius\Bundle\AdminBundle\Twig;

use Sylius\Component\Registry\ServiceRegistryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AttributeExtension extends AbstractExtension
{
    public function __construct(
        private readonly ServiceRegistryInterface $attributeTypeRegistry,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_attribute_types', [$this->attributeTypeRegistry, 'all']),
        ];
    }
}
