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

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariant;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductVariantSpec extends ObjectBehavior
{
    public function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductVariant::class);
    }

    function it_implements_sylius_product_variant_interface()
    {
        $this->shouldImplement(ProductVariantInterface::class);
    }

    function it_implements_sylius_resource_interface()
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    function it_initializes_option_values_collection_by_default()
    {
        $this->getOptionValues()->shouldHaveType(Collection::class);
    }

    function it_adds_an_option_value(ProductOptionValueInterface $optionValue)
    {
        $this->addOptionValue($optionValue);
        $this->hasOptionValue($optionValue)->shouldReturn(true);
    }

    function it_removes_an_option_value(ProductOptionValueInterface $optionValue)
    {
        $this->addOptionValue($optionValue);
        $this->removeOptionValue($optionValue);
        $this->hasOptionValue($optionValue)->shouldReturn(false);
    }

    function it_has_no_position_by_default()
    {
        $this->getPosition()->shouldReturn(null);
    }

    function its_position_is_mutable()
    {
        $this->setPosition(10);
        $this->getPosition()->shouldReturn(10);
    }
}
