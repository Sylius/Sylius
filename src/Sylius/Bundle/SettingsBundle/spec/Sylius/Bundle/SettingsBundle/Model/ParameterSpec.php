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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ParameterSpec extends ObjectBehavior
{
    public function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Model\Parameter');
    }

    public function it_should_be_a_Sylius_settings_parameter()
    {
        $this->shouldImplement('Sylius\Bundle\SettingsBundle\Model\ParameterInterface');
    }

    public function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_should_not_have_namespace_by_default()
    {
        $this->getNamespace()->shouldReturn(null);
    }

    public function its_namespace_should_be_mutable()
    {
        $this->setNamespace('general-settings');
        $this->getNamespace()->shouldReturn('general-settings');
    }

    public function it_should_be_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    public function its_name_should_be_mutable()
    {
        $this->setName('siteTitle');
        $this->getName()->shouldReturn('siteTitle');
    }

    public function it_should_not_have_value_by_default()
    {
        $this->getValue()->shouldReturn(null);
    }

    public function its_value_should_be_mutable()
    {
        $this->setValue(true);
        $this->getValue()->shouldReturn(true);
    }

    public function it_should_have_fluent_interface()
    {
        $this->setNamespace('taxation')->shouldReturn($this);
        $this->setName('enable')->shouldReturn($this);
        $this->setValue(true)->shouldReturn($this);
    }
}
