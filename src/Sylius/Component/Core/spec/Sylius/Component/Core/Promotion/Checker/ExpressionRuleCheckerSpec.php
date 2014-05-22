<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\ExpressionLanguage\ExpressionLanguageFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\UserInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ExpressionRuleCheckerSpec extends ObjectBehavior
{
    function let(ExpressionLanguageFactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\ExpressionRuleChecker');
    }

    function it_should_be_Sylius_rule_checker()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Checker\RuleCheckerInterface');
    }

    function it_should_evaluate_the_expression_stored_in_configuration(OrderInterface $order, UserInterface $user, ExpressionLanguage $expr, $factory)
    {
        $order->getUser()->shouldBeCalled()->willReturn($user);
        $factory->create()->shouldBeCalled()->willReturn($expr);

        $context = array('order' => $order, 'cart' => $order, 'user' => $user);

        $expr->evaluate('user.id = 5', $context)->shouldBeCalled()->willReturn(false);
        $this->isEligible($order, array('expr' => 'user.id = 5'))->shouldReturn(false);

        $expr->evaluate('user.id = 2', $context)->shouldBeCalled()->willReturn(true);
        $this->isEligible($order, array('expr' => 'user.id = 2'))->shouldReturn(true);
    }
}
