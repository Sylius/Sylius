<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\VariableProductBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariableProductBundle\Model\Option');
    }

    function it_implement_Sylius_option_interface()
    {
        $this->shouldImplement('Sylius\Bundle\VariableProductBundle\Model\OptionInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_have_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_should_be_mutable()
    {
        $this->setName('T-Shirt size');
        $this->getName()->shouldReturn('T-Shirt size');
    }

    function it_returns_name_when_converted_to_string()
    {
        $this->setName('T-Shirt color');
        $this->__toString()->shouldReturn('T-Shirt color');
    }

    function it_should_not_have_presentation_by_default()
    {
        $this->getPresentation()->shouldReturn(null);
    }

    function its_presentation_should_be_mutable()
    {
        $this->setPresentation('Size');
        $this->getPresentation()->shouldReturn('Size');
    }

    function it_should_initialize_values_collection_by_default()
    {
        $this->getValues()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    /**
     * @param Sylius\Bundle\VariableProductBundle\Model\OptionValueInterface $value
     */
    function it_should_add_value($value)
    {
        $value->setOption($this)->shouldBeCalled();

        $this->addValue($value);
        $this->hasValue($value)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\VariableProductBundle\Model\OptionValueInterface $value
     */
    function it_should_remove_value($value)
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
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function it_should_not_have_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
