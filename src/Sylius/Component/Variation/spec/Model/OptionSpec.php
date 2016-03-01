<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Variation\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Variation\Model\OptionInterface;
use Sylius\Component\Variation\Model\OptionValueInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OptionSpec extends ObjectBehavior
{
    public function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Variation\Model\Option');
    }

    function it_implement_Sylius_option_interface()
    {
        $this->shouldImplement(OptionInterface::class);
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_mutable_code()
    {
        $this->setCode('O1');
        $this->getCode()->shouldReturn('O1');
    }

    function it_should_not_have_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_should_be_mutable()
    {
        $this->setName('Size');
        $this->getName()->shouldReturn('Size');
    }

    function it_returns_name_when_converted_to_string()
    {
        $this->setName('T-Shirt color');
        $this->__toString()->shouldReturn('T-Shirt color');
    }

    function it_should_initialize_values_collection_by_default()
    {
        $this->getValues()->shouldHaveType(Collection::class);
    }

    function it_should_add_value(OptionValueInterface $value)
    {
        $value->setOption($this)->shouldBeCalled();

        $this->addValue($value);
        $this->hasValue($value)->shouldReturn(true);
    }

    function it_should_remove_value(OptionValueInterface $value)
    {
        $value->setOption($this)->shouldBeCalled();

        $this->addValue($value);
        $this->hasValue($value)->shouldReturn(true);

        $value->setOption(null)->shouldBeCalled();

        $this->removeValue($value);
        $this->hasValue($value)->shouldReturn(false);
    }

    function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType(\DateTime::class);
    }

    function it_should_not_have_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
