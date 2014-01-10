<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Checker\Registry;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Checker\RuleCheckerInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RuleCheckerRegistrySpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Checker\Registry\RuleCheckerRegistry');
    }

    function it_should_be_Sylius_rule_checker_registry()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\Checker\Registry\RuleCheckerRegistryInterface');
    }

    function it_should_initialize_checkers_array_by_default()
    {
        $this->getCheckers()->shouldReturn(array());
    }

    function it_should_register_checker_under_given_type(RuleCheckerInterface $checker)
    {
        $this->hasChecker('fake type')->shouldReturn(false);
        $this->registerChecker('fake type', $checker);
        $this->hasChecker('fake type')->shouldReturn(true);
    }

    function it_should_complain_if_trying_to_register_checker_with_taken_name(RuleCheckerInterface $checker)
    {
        $this->registerChecker('fake type', $checker);

        $this
            ->shouldThrow('Sylius\Bundle\ResourceBundle\Checker\Registry\ExistingRuleCheckerException')
            ->duringRegisterChecker('fake type', $checker)
        ;
    }

    function it_should_unregister_checker_with_given_name(RuleCheckerInterface $checker)
    {
        $this->registerChecker('fake type', $checker);
        $this->hasChecker('fake type')->shouldReturn(true);

        $this->unregisterChecker('fake type');
        $this->hasChecker('fake type')->shouldReturn(false);
    }

    function it_should_retrieve_registered_checker_by_name(RuleCheckerInterface $checker)
    {
        $this->registerChecker('fake type', $checker);
        $this->getChecker('fake type')->shouldReturn($checker);
    }

    function it_should_complain_if_trying_to_retrieve_non_existing_checker()
    {
        $this
            ->shouldThrow('Sylius\Bundle\ResourceBundle\Checker\Registry\NonExistingRuleCheckerException')
            ->duringGetChecker('fake type')
        ;
    }
}
