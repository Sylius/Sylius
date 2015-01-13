<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Report\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Łukasz Chruściel <lchrusciel@gmail.com>
 * @author Mateusz Zalewski <zaleslaw@gmail.com>
 */
class ReportSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Report\Model\Report');
    }

    function it_can_set_name()
    {
        $this->setName('testName');
        $this->getName()->shouldReturn('testName');
    }

    function it_can_set_description()
    {
        $this->setDescription('Test description for Report spec');
        $this->getDescription()->shouldReturn('Test description for Report spec');
    }
}
