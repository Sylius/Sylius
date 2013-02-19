<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SettingsBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Default parameter entity spec.
 *
 * @author Pawęł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DefaultParameter extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Entity\DefaultParameter');
    }

    function it_should_implement_Sylius_parameter_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SettingsBundle\Model\ParameterInterface');
    }

    function it_should_extend_Sylius_settings_parameter_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Entity\Parameter');
    }
}
