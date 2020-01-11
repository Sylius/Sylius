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
use Sylius\Component\Product\Model\ProductAssociationTypeTranslationInterface;
use Sylius\Component\Resource\Model\AbstractTranslation;

final class ProductAssociationTypeTranslationSpec extends ObjectBehavior
{
    function it_implements_a_product_association_type_translation_interface(): void
    {
        $this->shouldImplement(ProductAssociationTypeTranslationInterface::class);
    }

    function it_is_a_translation(): void
    {
        $this->shouldHaveType(AbstractTranslation::class);
    }

    function it_has_no_name_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Association type name');
        $this->getName()->shouldBe('Association type name');
    }
}
