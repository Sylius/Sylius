<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Promotion\Model\FilterInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class FilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Model\Filter');
    }

    function it_implements_filter_interface()
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function its_type_is_mutable()
    {
        $this->setType('taxon');
        $this->getType()->shouldReturn('taxon');
    }

    function its_action_is_mutable(ActionInterface $action)
    {
        $this->setAction($action);
        $this->getAction()->shouldReturn($action);
    }

    function its_configuration_is_mutable()
    {
        $this->setConfiguration(['id' => 4]);
        $this->getConfiguration()->shouldReturn(['id' => 4]);
    }
}
