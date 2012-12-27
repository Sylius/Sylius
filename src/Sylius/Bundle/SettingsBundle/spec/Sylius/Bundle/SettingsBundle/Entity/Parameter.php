<?php

namespace spec\Sylius\Bundle\SettingsBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Parameter mapped super-class spec.
 *
 * @author Pawęł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Parameter extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Entity\Parameter');
    }

    function it_should_be_a_Sylius_settings_parameter()
    {
        $this->shouldImplement('Sylius\Bundle\SettingsBundle\Model\ParameterInterface');
    }

    function it_should_extend_Sylius_settings_parameter_model()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Model\Parameter');
    }
}
