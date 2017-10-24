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

namespace spec\Sylius\Component\Product\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;

final class ProductAssociationTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_implements_association_type_interface(): void
    {
        $this->shouldImplement(ProductAssociationTypeInterface::class);
    }
}
