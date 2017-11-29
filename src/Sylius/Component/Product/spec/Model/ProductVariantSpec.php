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

final class ProductVariantSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    public function it_implements_sylius_product_variant_interface(): void
    {
        $this->shouldImplement(ProductVariantInterface::class);
    }

    public function it_implements_sylius_resource_interface(): void
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    public function it_initializes_option_values_collection_by_default(): void
    {
        $this->getOptionValues()->shouldHaveType(Collection::class);
    }

    public function it_adds_an_option_value(ProductOptionValueInterface $optionValue): void
    {
        $this->addOptionValue($optionValue);
        $this->hasOptionValue($optionValue)->shouldReturn(true);
    }

    public function it_removes_an_option_value(ProductOptionValueInterface $optionValue): void
    {
        $this->addOptionValue($optionValue);
        $this->removeOptionValue($optionValue);
        $this->hasOptionValue($optionValue)->shouldReturn(false);
    }

    public function it_has_no_position_by_default(): void
    {
        $this->getPosition()->shouldReturn(null);
    }

    public function its_position_is_mutable(): void
    {
        $this->setPosition(10);
        $this->getPosition()->shouldReturn(10);
    }
}
