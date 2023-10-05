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

namespace spec\Sylius\Bundle\ApiBundle\PropertyInfo\Extractor;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Symfony\Component\PropertyInfo\PropertyListExtractorInterface;

final class EmptyPropertyListExtractorSpec extends ObjectBehavior
{
    function it_is_property_list_extractor(): void
    {
        $this->shouldImplement(PropertyListExtractorInterface::class);
    }

    function it_provides_empty_list_if_requested_class_exists(): void
    {
        $this->getProperties(PickupCart::class, [])->shouldReturn([]);
    }

    function it_provides_null_if_requested_class_does_not_exist(): void
    {
        $this->getProperties(\Serializable::class, [])->shouldReturn(null);
    }
}
