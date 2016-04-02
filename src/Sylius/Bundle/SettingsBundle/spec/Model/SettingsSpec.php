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
use Sylius\Bundle\SettingsBundle\Exception\ParameterNotFoundException;
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

    function it_implements_array_access_interface()
    {
        $this->shouldImplement(\ArrayAccess::class);
    }

    function it_implements_countable_interface()
    {
        $this->shouldImplement(\Countable::class);
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

    function its_namespace_is_null_by_default()
    {
        $this->getNamespace()->shouldReturn(null);
    }

    function its_namespace_should_be_immutable_after_it_is_set()
    {
        $this->setNamespace('banana');
        $this->getNamespace()->shouldReturn('banana');
        $this
            ->shouldThrow(new \LogicException('The namespace of the settings model is immutable, instantiate a new object in order to use another namespace.'))
            ->during('setNamespace', ['i_dont_like_to_be_changed'])
        ;
    }

    function its_parameters_has_empty_array_by_default()
    {
        $this->getParameters()->shouldReturn([]);
    }

    function it_can_set_a_parameter()
    {
        $this->set('key', 'value');
        $this->getParameters()->shouldReturn([
            'key' => 'value',
        ])
        ;
    }

    function it_throws_parameter_not_found_exception_when_getting_non_existing_parameter()
    {
        $this->shouldThrow(ParameterNotFoundException::class)->during('get', ['non_existing']);
    }

    function it_can_get_a_parameter()
    {
        $this->set('key', 'value');
        $this->get('key')->shouldReturn('value');
    }

    function it_can_check_if_it_has_parameter()
    {
        $this->has('key')->shouldReturn(false);
        $this->set('key', 'value');
        $this->has('key')->shouldReturn(true);
    }

    function it_throws_parameter_not_found_exception_when_removing_non_existing_parameter()
    {
        $this->shouldThrow(ParameterNotFoundException::class)->during('remove', ['non_existing']);
    }

    function it_can_remove_a_parameter()
    {
        $this->set('key', 'value');
        $this->remove('key');
        $this->has('key')->shouldReturn(false);
    }

    function it_can_count_its_parameters()
    {
        $this->count()->shouldReturn(0);
        $this->set('key', 'value');
        $this->count()->shouldReturn(1);
    }
}
