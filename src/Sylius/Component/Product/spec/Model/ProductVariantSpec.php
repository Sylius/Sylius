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

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

final class ProductVariantSpec extends ObjectBehavior
{
    function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_implements_sylius_product_variant_interface(): void
    {
        $this->shouldImplement(ProductVariantInterface::class);
    }

    function it_implements_toggleable_interface(): void
    {
        $this->shouldImplement(ToggleableInterface::class);
    }

    function it_implements_sylius_resource_interface(): void
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    function it_initializes_option_values_collection_by_default(): void
    {
        $this->getOptionValues()->shouldHaveType(Collection::class);
    }

    function it_adds_an_option_value(ProductOptionValueInterface $optionValue): void
    {
        $this->addOptionValue($optionValue);
        $this->hasOptionValue($optionValue)->shouldReturn(true);
    }

    function it_removes_an_option_value(ProductOptionValueInterface $optionValue): void
    {
        $this->addOptionValue($optionValue);
        $this->removeOptionValue($optionValue);
        $this->hasOptionValue($optionValue)->shouldReturn(false);
    }

    function it_has_no_position_by_default(): void
    {
        $this->getPosition()->shouldReturn(null);
    }

    function its_position_is_mutable(): void
    {
        $this->setPosition(10);
        $this->getPosition()->shouldReturn(10);
    }

    function it_is_enabled_by_default(): void
    {
        $this->shouldBeEnabled();
    }

    function it_is_toggleable(): void
    {
        $this->disable();
        $this->shouldNotBeEnabled();

        $this->enable();
        $this->shouldBeEnabled();
    }
}
