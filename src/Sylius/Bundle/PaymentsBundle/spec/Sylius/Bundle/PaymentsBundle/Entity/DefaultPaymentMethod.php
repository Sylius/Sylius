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
 * Payment method mapped super-class spec.
 *
 * @author Pawęł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DefaultPaymentMethod extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentsBundle\Entity\PaymentMethod');
    }

    function it_should_implement_Sylius_payment_method_interface()
    {
        $this->shouldImplement('Sylius\Bundle\PaymentsBundle\Model\PaymentMethodInterface');
    }

    function it_should_extend_Sylius_payment_method_model()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentsBundle\Model\PaymentMethod');
    }
}
