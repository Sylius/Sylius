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
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValue;

final class ProductOptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_implements_product_option_interface(): void
    {
        $this->shouldHaveType(ProductOptionInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_code_by_default(): void
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('color');
        $this->getCode()->shouldReturn('color');
    }

    function it_has_no_name_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Color');
        $this->getName()->shouldReturn('Color');
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

    function it_has_an_empty_collection_of_values_by_default(): void
    {
        $this->getValues()->shouldHaveType(Collection::class);
        $this->getValues()->count()->shouldReturn(0);
    }

    function it_can_have_a_value_added(ProductOptionValue $value): void
    {
        $this->addValue($value);
        $this->hasValue($value)->shouldReturn(true);
    }

    function it_can_have_a_locale_removed(ProductOptionValue $value): void
    {
        $this->addValue($value);
        $this->removeValue($value);
        $this->hasValue($value)->shouldReturn(false);
    }
}
