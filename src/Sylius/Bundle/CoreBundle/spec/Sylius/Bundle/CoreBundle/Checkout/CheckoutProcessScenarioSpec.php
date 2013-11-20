<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Checkout;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CheckoutProcessScenarioSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\CartBundle\Provider\CartProviderInterface $cartProvider
     * @param Sylius\Bundle\CartBundle\Model\CartInterface            $cart
     */
    function let($cartProvider, $cart)
    {
        $cartProvider->getCart()->willReturn($cart);

        $this->beConstructedWith($cartProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Checkout\CheckoutProcessScenario');
    }

    function it_implements_Sylius_process_scenario_interface()
    {
        $this->shouldImplement('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface');
    }

    /**
     * @param Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilderInterface $builder
     */
    function it_builds_checkout_process_with_proper_steps($builder)
    {
        $builder->add('security', 'sylius_checkout_security')->willReturn($builder)->shouldBeCalled();
        $builder->add('addressing', 'sylius_checkout_addressing')->willReturn($builder)->shouldBeCalled();
        $builder->add('shipping', 'sylius_checkout_shipping')->willReturn($builder)->shouldBeCalled();
        $builder->add('payment', 'sylius_checkout_payment')->willReturn($builder)->shouldBeCalled();
        $builder->add('finalize', 'sylius_checkout_finalize')->willReturn($builder)->shouldBeCalled();
        $builder->add("purchase", "sylius_checkout_purchase")->willReturn($builder)->shouldBeCalled();

        $builder->setDisplayRoute('sylius_checkout_display')->willReturn($builder)->shouldBeCalled();
        $builder->setForwardRoute('sylius_checkout_forward')->willReturn($builder)->shouldBeCalled();

        $builder->setRedirect('sylius_homepage')->willReturn($builder)->shouldBeCalled();
        $builder->validate(Argument::any())->willReturn($builder)->shouldBeCalled();

        $this->build($builder);
    }
}
