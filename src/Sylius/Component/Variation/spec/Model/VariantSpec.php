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
use Sylius\Component\Variation\Model\OptionValueInterface;
use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class VariantSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Variation\Model\Variant');
    }

    function it_implements_Sylius_variant_interface()
    {
        $this->shouldImplement(VariantInterface::class);
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_belong_to_a_variable_by_default()
    {
        $this->getObject()->shouldReturn(null);
    }

    function it_should_allow_assigning_itself_to_a_variable(VariableInterface $variable)
    {
        $this->setObject($variable);
        $this->getObject()->shouldReturn($variable);
    }

    function it_should_allow_detaching_itself_from_a_variable(VariableInterface $variable)
    {
        $this->setObject($variable);
        $this->getObject()->shouldReturn($variable);

        $this->setObject(null);
        $this->getObject()->shouldReturn(null);
    }

    function it_should_not_have_presentation_by_default()
    {
        $this->getPresentation()->shouldReturn(null);
    }

    function its_presentation_should_be_mutable()
    {
        $this->setPresentation('Super variant');
        $this->getPresentation()->shouldReturn('Super variant');
    }

    function it_should_initialize_option_values_collection_by_default()
    {
        $this->getOptions()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function its_option_values_collection_should_be_mutable(Collection $options)
    {
        $this->setOptions($options);
        $this->getOptions()->shouldReturn($options);
    }

    function it_should_add_option_value_properly(OptionValueInterface $option)
    {
        $this->addOption($option);
        $this->hasOption($option)->shouldReturn(true);
    }

    function it_should_remove_option_value_properly(OptionValueInterface $option)
    {
        $this->addOption($option);
        $this->hasOption($option)->shouldReturn(true);

        $this->removeOption($option);
        $this->hasOption($option)->shouldReturn(false);
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
