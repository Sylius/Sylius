<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Product\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductVariantTranslation;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
use Sylius\Component\Resource\Model\AbstractTranslation;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ProductVariantTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductVariantTranslation::class);
    }

    function it_implements_product_variant_translation_interface()
    {
        $this->shouldImplement(ProductVariantTranslationInterface::class);
    }

    function it_is_translation()
    {
        $this->shouldHaveType(AbstractTranslation::class);
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Super variant');
        $this->getName()->shouldReturn('Super variant');
    }
}
