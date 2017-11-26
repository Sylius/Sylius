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
    public function let(): void
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    public function it_implements_product_option_interface(): void
    {
        $this->shouldHaveType(ProductOptionInterface::class);
    }

    public function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_code_by_default(): void
    {
        $this->getCode()->shouldReturn(null);
    }

    public function its_code_is_mutable(): void
    {
        $this->setCode('color');
        $this->getCode()->shouldReturn('color');
    }

    public function it_has_no_name_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    public function its_name_is_mutable(): void
    {
        $this->setName('Color');
        $this->getName()->shouldReturn('Color');
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

    public function it_has_an_empty_collection_of_values_by_default(): void
    {
        $this->getValues()->shouldHaveType(Collection::class);
        $this->getValues()->count()->shouldReturn(0);
    }

    public function it_can_have_a_value_added(ProductOptionValue $value): void
    {
        $this->addValue($value);
        $this->hasValue($value)->shouldReturn(true);
    }

    public function it_can_have_a_locale_removed(ProductOptionValue $value): void
    {
        $this->addValue($value);
        $this->removeValue($value);
        $this->hasValue($value)->shouldReturn(false);
    }
}
