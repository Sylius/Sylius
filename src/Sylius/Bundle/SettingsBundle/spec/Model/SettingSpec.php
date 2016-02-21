<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SettingsBundle\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\SettingsBundle\Model\ParameterInterface;
use Sylius\Bundle\SettingsBundle\Model\SettingInterface;
use Doctrine\Common\Collections\Collection;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class SettingSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Model\Setting');
    }

    function it_implements_setting_interface()
    {
        $this->shouldImplement(SettingInterface::class);
    }

    function it_initializes_parameter_collection_by_default()
    {
        $this->getParameters()->shouldHaveType(Collection::class);
    }

    function its_schema_is_null_by_default()
    {
        $this->getSchema()->shouldReturn(null);
    }

    function its_schema_should_be_immutable_after_it_is_set()
    {
        $this->setSchema('theme');
        $this->getSchema()->shouldReturn('theme');

        $this
            ->shouldThrow(new \LogicException('A settings schema is immutable, you have to define a new "Setting" object to use another schema.'))
            ->during('setSchema', ['i_dont_like_to_be_changed'])
        ;
    }

    function it_can_get_parameters(ParameterInterface $parameter)
    {
        $this->addParameter($parameter);
        $this->getParameters()->shouldHaveCount(1);
    }

    function it_can_get_parameter_by_name(ParameterInterface $parameter)
    {
        $parameter->getName()
            ->shouldBeCalled()
            ->willReturn('left_side_products')
        ;

        $this->addParameter($parameter);

        $this->getParameter('left_side_products')
            ->shouldReturn($parameter)
        ;
    }

    function it_can_check_if_it_has_a_parameter(ParameterInterface $parameter)
    {
        $this->addParameter($parameter);
        $this->hasParameter($parameter)->shouldReturn(true);
    }

    function it_allows_to_add_parameters(ParameterInterface $parameter)
    {
        $this->addParameter($parameter);
        $this->getParameters()->shouldHaveCount(1);
    }

    function it_allows_to_remove_parameters(ParameterInterface $parameter)
    {
        $this->addParameter($parameter);
        $this->removeParameter($parameter);
        $this->getParameters()->shouldHaveCount(0);
    }

    function it_prevents_adding_parameter_when_it_already_has_it(ParameterInterface $parameter)
    {
        $this->addParameter($parameter);
        $this->getParameters()->shouldHaveCount(1);

        $this->addParameter($parameter);
        $this->getParameters()->shouldHaveCount(1);
    }
}
