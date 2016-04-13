<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Checker\CartQuantityRuleChecker;
use Sylius\Component\Promotion\Checker\ItemTotalRuleChecker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RuleChoiceTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            ItemTotalRuleChecker::TYPE => 'Order total',
            CartQuantityRuleChecker::TYPE => 'Order quantity',
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionBundle\Form\Type\RuleChoiceType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_should_set_rule_types_to_choose_from(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['choices' => [
            ItemTotalRuleChecker::TYPE => 'Order total',
            CartQuantityRuleChecker::TYPE => 'Order quantity',
        ]])->shouldBeCalled();

        $this->configureOptions($resolver);
    }
}
