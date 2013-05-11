<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PaymentsBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Credit Card spec.
 *
 * @author Dylan Johnson <eponymi.dev@gmail.com>
 */
class CreditCard extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentsBundle\Entity\CreditCard');
    }

    function it_implements_Sylius_credit_card_interface()
    {
        $this->shouldImplement('Sylius\Bundle\PaymentsBundle\Model\CreditCardInterface');
    }

    function it_extends_Sylius_credit_card_model()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentsBundle\Model\CreditCard');
    }
}
