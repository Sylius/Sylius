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
use Sylius\Component\Product\Model\ProductOption;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValue;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductOptionSpec extends ObjectBehavior
{
    public function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductOption::class);
    }

    function it_implements_product_option_interface()
    {
        $this->shouldHaveType(ProductOptionInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable()
    {
        $this->setCode('color');
        $this->getCode()->shouldReturn('color');
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Color');
        $this->getName()->shouldReturn('Color');
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

    function it_has_an_empty_collection_of_values_by_default()
    {
        $this->getValues()->shouldHaveType(Collection::class);
        $this->getValues()->count()->shouldReturn(0);
    }

    function it_can_have_a_value_added(ProductOptionValue $value)
    {
        $this->addValue($value);
        $this->hasValue($value)->shouldReturn(true);
    }

    function it_can_have_a_locale_removed(ProductOptionValue $value)
    {
        $this->addValue($value);
        $this->removeValue($value);
        $this->hasValue($value)->shouldReturn(false);
    }
}
