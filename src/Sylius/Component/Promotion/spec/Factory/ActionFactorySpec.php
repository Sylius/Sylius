<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Factory\ActionFactoryInterface;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ActionFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $actionFactory)
    {
        $this->beConstructedWith($actionFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Factory\ActionFactory');
    }

    function it_implements_action_factory_interface()
    {
        $this->shouldImplement(ActionFactoryInterface::class);
    }

    function it_creates_new_action_with_default_action_factory($actionFactory, ActionInterface $action)
    {
        $actionFactory->createNew()->willReturn($action);

        $this->createNew()->shouldReturn($action);
    }

    function it_creates_new_fixed_discount_action_with_given_amount($actionFactory, ActionInterface $action)
    {
        $actionFactory->createNew()->willReturn($action);

        $action->setType(ActionInterface::TYPE_FIXED_DISCOUNT)->shouldBeCalled();
        $action->setConfiguration(['amount' => 1000])->shouldBeCalled();

        $this->createFixedDiscount(1000)->shouldReturn($action);
    }
}
