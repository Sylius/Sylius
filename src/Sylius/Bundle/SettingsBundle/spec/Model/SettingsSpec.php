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

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SettingsBundle\Model\ParameterInterface;
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class SettingsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Model\Settings');
    }

    function it_implements_settings_interface()
    {
        $this->shouldImplement(SettingsInterface::class);
    }

    function it_initializes_parameter_collection_by_default()
    {
        $this->getParameters()->shouldHaveType(Collection::class);
    }

    function its_schema_alias_is_null_by_default()
    {
        $this->getSchemaAlias()->shouldReturn(null);
    }

    function its_schema_should_be_immutable_after_it_is_set()
    {
        $this->setSchemaAlias('theme');
        $this->getSchemaAlias()->shouldReturn('theme');
        $this
            ->shouldThrow(new \LogicException('The schema alias of the settings model is immutable, instantiate a new object in order to use another schema.'))
            ->during('setSchemaAlias', ['i_dont_like_to_be_changed'])
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

        $parameter->setSettings($this)
            ->shouldBeCalled()
        ;

        $this->addParameter($parameter);

        $this->getParameter('left_side_products')
            ->shouldReturn($parameter)
        ;
    }

    function it_can_check_if_it_has_a_parameter(ParameterInterface $parameter)
    {
        $parameter->setSettings($this)
            ->shouldBeCalled()
        ;

        $parameter->getName()
            ->willReturn('title')
        ;

        $this->addParameter($parameter);

        $this->hasParameter('title')->shouldReturn(true);
        $this->hasParameter('non_existing')->shouldReturn(false);
    }

    function it_allows_to_add_parameters(ParameterInterface $parameter)
    {
        $parameter->setSettings($this)
            ->shouldBeCalled()
        ;

        $parameter->getName()
            ->shouldBeCalled()
            ->willReturn('title')
        ;

        $this->addParameter($parameter);
        $this->getParameters()->shouldHaveCount(1);
    }

    function it_allows_to_remove_parameters(ParameterInterface $parameter)
    {
        $parameter->setSettings($this)
            ->shouldBeCalled()
        ;

        $parameter->getName()
            ->shouldBeCalled()
            ->willReturn('title')
        ;

        $this->addParameter($parameter);

        $this->removeParameter($parameter);
        $this->getParameters()->shouldHaveCount(0);
    }
}
