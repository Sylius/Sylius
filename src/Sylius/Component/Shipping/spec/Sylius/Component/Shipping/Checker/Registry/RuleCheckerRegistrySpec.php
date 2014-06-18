<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Shipping\Checker\Registry;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Checker\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\RuleInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RuleCheckerRegistrySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Checker\Registry\RuleCheckerRegistry');
    }

    function it_is_Sylius_rule_checker_registry()
    {
        $this->shouldImplement('Sylius\Component\Shipping\Checker\Registry\RuleCheckerRegistryInterface');
    }

    function it_should_initialize_checkers_array_by_default()
    {
        $this->getCheckers()->shouldReturn(array());
    }

    function it_should_register_checker_under_given_type(RuleCheckerInterface $checker)
    {
        $this->hasChecker(RuleInterface::TYPE_ITEM_TOTAL)->shouldReturn(false);
        $this->registerChecker(RuleInterface::TYPE_ITEM_TOTAL, $checker);
        $this->hasChecker(RuleInterface::TYPE_ITEM_TOTAL)->shouldReturn(true);
    }

    function it_should_complain_if_trying_to_register_checker_with_taken_name(RuleCheckerInterface $checker)
    {
        $this->registerChecker(RuleInterface::TYPE_ITEM_TOTAL, $checker);

        $this
            ->shouldThrow('Sylius\Component\Shipping\Checker\Registry\ExistingRuleCheckerException')
            ->duringRegisterChecker(RuleInterface::TYPE_ITEM_TOTAL, $checker)
        ;
    }

    function it_should_unregister_checker_with_given_name(RuleCheckerInterface $checker)
    {
        $this->registerChecker(RuleInterface::TYPE_ITEM_TOTAL, $checker);
        $this->hasChecker(RuleInterface::TYPE_ITEM_TOTAL)->shouldReturn(true);

        $this->unregisterChecker(RuleInterface::TYPE_ITEM_TOTAL);
        $this->hasChecker(RuleInterface::TYPE_ITEM_TOTAL)->shouldReturn(false);
    }

    function it_should_retrieve_registered_checker_by_name(RuleCheckerInterface $checker)
    {
        $this->registerChecker(RuleInterface::TYPE_ITEM_TOTAL, $checker);
        $this->getChecker(RuleInterface::TYPE_ITEM_TOTAL)->shouldReturn($checker);
    }

    function it_should_complain_if_trying_to_retrieve_non_existing_checker()
    {
        $this
            ->shouldThrow('Sylius\Component\Shipping\Checker\Registry\NonExistingRuleCheckerException')
            ->duringGetChecker(RuleInterface::TYPE_ITEM_TOTAL)
        ;
    }
}
