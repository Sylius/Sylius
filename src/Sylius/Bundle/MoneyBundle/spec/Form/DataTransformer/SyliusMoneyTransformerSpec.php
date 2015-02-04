<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MoneyBundle\Form\DataTransformer;

use PhpSpec\ObjectBehavior;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class SyliusMoneyTransformerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Form\DataTransformer\SyliusMoneyTransformer');
    }

    function it_extends_money_to_localized_string_transformer_class()
    {
        $this->shouldHaveType('Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer');
    }

    function it_returns_null_if_empty_string_given()
    {
        $this->reverseTransform('')->shouldReturn(null);
    }

    function it_converts_string_to_an_int()
    {
        $this->beConstructedWith(null, null, null, 100);
        $this->reverseTransform('4.10')->shouldReturn(410);
    }
}
